<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Cobweb\Svconnector\Service;

use Cobweb\Svconnector\Domain\Model\Dto\CallContext;
use Cobweb\Svconnector\Domain\Model\Dto\ConnectionInformation;
use Cobweb\Svconnector\Event\InitializeConnectorEvent;
use Cobweb\Svconnector\Event\PostProcessOperationsEvent;
use Cobweb\Svconnector\Event\ProcessParametersEvent;
use Cobweb\Svconnector\Exception\ConnectorRuntimeException;
use Cobweb\Svconnector\Utility\ParameterParser;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The "Connector Services" family of services is designed to handle connections
 * to external servers and pass request to and from those servers, using whatever
 * protocols are appropriate.
 *
 * This class is a base class for all Connector Services. It should be inherited
 * by all specific Connector Services implementations. This class should not be called
 * directly as it is unable to do anything by itself.
 */
abstract class ConnectorBase implements LoggerAwareInterface, ConnectorServiceInterface
{
    use LoggerAwareTrait;

    protected string $extensionKey = 'svconnector';
    // Information set from outside the connector service, to give context about where it is being called from
    protected CallContext $callContext;
    // Information about the connection that the service tries to establish
    protected ConnectionInformation $connectionInformation;
    protected EventDispatcher $eventDispatcher;
    protected LanguageService $languageService;
    protected string $type = '';
    protected string $name = '';
    protected array $parameters = [];

    public function __construct()
    {
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $this->callContext = new CallContext();
        $this->connectionInformation = new ConnectionInformation();
        $this->initializeLanguageService();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Perform any necessary initialization of the service and fire an event to allow
     * for custom handling in specific cases
     */
    public function initialize(): void
    {
        $this->eventDispatcher->dispatch(
            new InitializeConnectorEvent($this)
        );
    }

    /**
     * Set the connector parameters, process them and fire an event for custom manipulation
     */
    public function setParameters(array $parameters): void
    {
        $parameterParser = GeneralUtility::makeInstance(ParameterParser::class);
        $parameters = $parameterParser->parse(
            $parameters,
            $this->connectionInformation->get()
        );
        $event = $this->eventDispatcher->dispatch(
            new ProcessParametersEvent($parameters)
        );
        $this->parameters = $event->getParameters();
    }

    /**
     * Return the defined connector parameters
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Return the current call context object
     */
    public function getCallContext(): CallContext
    {
        return $this->callContext;
    }

    /**
     * Return the current connection information
     */
    public function getConnectionInformation(): ConnectionInformation
    {
        return $this->connectionInformation;
    }

    /**
     * Verifies that the connection is functional
     * Returns false if not. This base implementation always returns false,
     * since it is not supposed to be called directly
     *
     * @return bool TRUE if the service is available
     */
    abstract public function isAvailable(): bool;

    /**
     * Returns the sample configuration for the service, if any
     *
     * @return string
     */
    public function getSampleConfiguration(): string
    {
        $configurationFile = ExtensionManagementUtility::extPath(
            $this->extensionKey,
            'Resources/Public/Samples/Configuration.json'
        );
        if (file_exists($configurationFile)) {
            return file_get_contents($configurationFile);
        }
        return '';
    }

    /**
     * Checks the connector configuration and returns notices, warnings or errors, if any.
     *
     * This base method needs to be implemented for each actual service.
     *
     * @return array
     */
    public function checkConfiguration(): array
    {
        return [
            ContextualFeedbackSeverity::NOTICE->value => [],
            ContextualFeedbackSeverity::WARNING->value => [],
            ContextualFeedbackSeverity::ERROR->value => [],
        ];
    }

    /**
     * Logs all problems reported by checkConfiguration().
     *
     * @param array $problems
     */
    public function logConfigurationCheck(array $problems): void
    {
        foreach ($problems as $severity => $issues) {
            foreach ($issues as $issue) {
                switch ($severity) {
                    case ContextualFeedbackSeverity::ERROR->value:
                        $this->logger->error($issue);
                        break;
                    case ContextualFeedbackSeverity::WARNING->value:
                        $this->logger->warning($issue);
                        break;
                    default:
                        $this->logger->notice($issue);
                }
            }
        }
    }

    abstract public function fetchRaw(): mixed;

    abstract public function fetchXML(): string;

    abstract public function fetchArray(): array;

    /**
     * This method can be called to perform specific operations at some point after
     * any of the fetch methods have been called. The base implementation does nothing
     * but fire an event.
     *
     * @param mixed $status Some form of status can be passed as argument
     *                      The nature of that status will depend on which process is calling this method
     */
    public function postProcessOperations(mixed $status): void
    {
        $this->eventDispatcher->dispatch(
            new PostProcessOperationsEvent($status, $this)
        );
    }

    /**
     * Queries the distant server given some parameters and returns the server response.
     *
     * Note that this is not officially part of the Connector Service API. However, all fetch*
     * methods will need the same logic for retrieving the data, and the customary practice is
     * to centralize that code in a method called query().
     *
     * Look at the existing connector services for implementation examples.
     *
     * @return mixed Server response
     */
    abstract protected function query(): mixed;

    /**
     * This method should be used by all connector services when they encounter a fatal error.
     * It will throw an exception and send the error to the logging API.
     *
     * @param string $message Error message
     * @param int $exceptionNumber Number (code) of the exception
     * @param array $extraData Additional data to be passed to the log
     * @param string $exceptionClass Name of the class of exception which should be thrown
     * @throws \Exception
     */
    protected function raiseError(
        string $message,
        int $exceptionNumber,
        array $extraData = [],
        string $exceptionClass = ConnectorRuntimeException::class
    ): never {
        $this->logger->error($message, $extraData);
        throw new $exceptionClass($message, $exceptionNumber);
    }

    /**
     * Wrapper around the "sL()" method to abstract context.
     *
     * If no language object exists, which should not happen, the key
     * is returned as.
     *
     * @param string $key "LLL:" input key
     * @return string The translated string
     */
    public function sL(string $key): string
    {
        return $this->languageService->sL($key);
    }

    /**
     * Gets the currently used character set depending on context. TYPO3 always runs with UTF-8.
     *
     * @return string
     */
    public function getCharset(): string
    {
        return 'utf-8';
    }

    /**
     * Initializes the LanguageService depending on context, falling back to default language if all else fails
     */
    protected function initializeLanguageService(): void
    {
        try {
            $request = $this->getTypo3Request();
            $applicationType = ApplicationType::fromRequest($request);
            if ($applicationType->isFrontend()) {
                $this->languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)
                    ->createFromSiteLanguage(
                        $request->getAttribute('language')
                    );
            } else {
                $this->languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)
                    ->createFromUserPreferences(
                        $this->getBackendUser()
                    );
            }
        } catch (\Exception) {
            $this->languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('en');
        }
    }

    /**
     * Wrapper around the global TYPO3 request object
     *
     * Throws an exception if none is found (seems to happen depending on context)
     *
     * @return ServerRequestInterface
     */
    protected function getTypo3Request(): ServerRequestInterface
    {
        if (isset($GLOBALS['TYPO3_REQUEST']) && $GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            return $GLOBALS['TYPO3_REQUEST'];
        }
        throw new \InvalidArgumentException('Global request object not found');
    }

    /**
     * Wrapper around the global BE user object
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
