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
use Cobweb\Svconnector\Event\ProcessParametersEvent;
use Cobweb\Svconnector\Exception\ConnectorRuntimeException;
use Cobweb\Svconnector\Utility\ParameterParser;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
    protected array $parameters = [];

    public function __construct()
    {
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $this->callContext = new CallContext();
        $this->connectionInformation = new ConnectionInformation();
        $this->initializeLanguageService();
    }

    /**
     * Returns the type of data handled by the connector service
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Returns a descriptive name of the connector service
     *
     * @return string
     */
    abstract public function getName(): string;

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
     * @param array $parameters Connector call parameters
     * @return array
     */
    public function checkConfiguration(array $parameters = []): array
    {
        // Temporary code while passing parameters array is deprecated and before
        // method signature is changed entirely (in the next major version)
        // Base deprecation code that can be called by all inheriting classes
        if (count(func_get_args()) > 0) {
            $this->triggerDeprecation('fetchRaw()');
            $this->parameters = $parameters;
        }

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

    /**
     * This method calls the query and returns the results from the response as is.
     *
     * You need to implement your own fetchRaw() method when creating a connector service.
     * It is recommended to place a hook in it, to allow for custom manipulations of the
     * received data.
     * Look at the existing connector services for examples.
     *
     * @param array $parameters Parameters for the call
     * @return mixed Server response
     */
    public function fetchRaw(array $parameters = [])
    {
        // Temporary code while passing parameters array is deprecated and before
        // method signature is changed entirely (in the next major version)
        // Base deprecation code that can be called by all inheriting classes
        if (count(func_get_args()) > 0) {
            $this->triggerDeprecation('fetchRaw()');
            $this->parameters = $parameters;
        }
        return '';
    }

    /**
     * This method calls the query and returns the results from the response as an XML structure.
     *
     * You need to implement your own fetchXML() method when creating a connector service.
     * You can rely on \TYPO3\CMS\Core\Utility\GeneralUtility::array2xml_cs() to convert
     * an array response to XML.
     * It is recommended to place a hook in it, to allow for custom manipulations of the
     * received data.
     * Look at the existing connector services for examples.
     *
     * @param array $parameters Parameters for the call
     * @return string XML structure
     */
    public function fetchXML(array $parameters = []): string
    {
        // Temporary code while passing parameters array is deprecated and before
        // method signature is changed entirely (in the next major version)
        // Base deprecation code that can be called by all inheriting classes
        if (count(func_get_args()) > 0) {
            $this->triggerDeprecation('fetchRaw()');
            $this->parameters = $parameters;
        }
        return '';
    }

    /**
     * This method calls the query and returns the results from the response as a PHP array.
     *
     * You need to implement your own fetchArray() method when creating a connector service.
     * You can rely on \tx_svconnector_utility::convertXmlToArray() to convert
     * an XML response to an array.
     * It is recommended to place a hook in it, to allow for custom manipulations of the
     * received data.
     * Look at the existing connector services for examples.
     *
     * @param array $parameters Parameters for the call
     * @return array PHP array
     */
    public function fetchArray(array $parameters = []): array
    {
        // Temporary code while passing parameters array is deprecated and before
        // method signature is changed entirely (in the next major version)
        // Base deprecation code that can be called by all inheriting classes
        if (count(func_get_args()) > 0) {
            $this->triggerDeprecation('fetchRaw()');
            $this->parameters = $parameters;
        }
        return [];
    }

    /**
     * This method can be called to perform specific operations at some point after
     * any of the fetch methods have been called. It does nothing by itself,
     * but provides a hook for custom post-processing
     *
     * @param array $parameters Parameters for the call
     * @param mixed $status Some form of status can be passed as argument
     *                      The nature of that status will depend on which process is calling this method
     */
    public function postProcessOperations(array $parameters, mixed $status): void
    {
        // Temporary code while passing parameters array is deprecated and before
        // method signature is changed entirely (in the next major version)
        if (count($parameters) > 0) {
            $this->triggerDeprecation('fetchRaw()');
            $this->parameters = $parameters;
        }

        $hooks = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extensionKey]['postProcessOperations'] ?? null;
        if (is_array($hooks)) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$this->extensionKey]['postProcessOperations'] as $className) {
                $processor = GeneralUtility::makeInstance($className);
                $processor->postProcessOperations($this->parameters, $status, $this);
            }
        }
    }

    /**
     * Queries the distant server given some parameters and returns the server response.
     *
     * You need to implement your own query() method when creating a connector service. It will be called
     * by all fetch*() methods.
     *
     * It is recommended to put some events in your code, because actual use cases from users
     * may differ from your own. An event to process parameters and an event to process the response
     * seem useful in general.
     *
     * Look at the existing connector services for implementation examples.
     *
     * @param array $parameters Parameters for the call
     *
     * @return mixed Server response
     */
    protected function query(array $parameters = [])
    {
        // Temporary code while passing parameters array is deprecated and before
        // method signature is changed entirely (in the next major version)
        // Base deprecation code that can be called by all inheriting classes
        if (count(func_get_args()) > 0) {
            $this->triggerDeprecation('fetchRaw()');
            $this->parameters = $parameters;
        }
        return '';
    }

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
    protected function raiseError($message, $exceptionNumber, array $extraData = [], $exceptionClass = ConnectorRuntimeException::class): never
    {
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
     * Get an existing instance of the charset conversion class, depending on context.
     *
     * @throws \Exception
     * @return CharsetConverter
     */
    public function getCharsetConverter(): CharsetConverter
    {
        return GeneralUtility::makeInstance(CharsetConverter::class);
    }

    /**
     * Initializes the LanguageService depending on context, falling back to default language if all else fails
     */
    protected function initializeLanguageService(): void
    {
        try {
            $applicationType = ApplicationType::fromRequest(
                $this->getTypo3Request()
            );
            if ($applicationType->isFrontend()) {
                $this->languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)
                    ->createFromSiteLanguage(
                        $this->getTyposcriptFrontendController()->getLanguage()
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
     * Wrapper around the global frontend controller object
     *
     * @return TypoScriptFrontendController
     */
    protected function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
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

    /**
     * @internal
     * @deprecated Will be removed in next major version
     */
    public function triggerDeprecation(string $method): void
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = end($backtrace);
        $callerLocation = sprintf('file %s, line %d', $caller['file'], $caller['line']);

        trigger_error(sprintf(
            'Passing parameters as argument to %s is deprecated. Pass arguments when getting service from registry instead. Location: %s',
            $method,
            $callerLocation,
        ), E_USER_DEPRECATED);
    }
}
