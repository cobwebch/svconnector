<?php

declare(strict_types=1);

namespace Cobweb\Svconnector\Controller;

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

use Cobweb\Svconnector\Registry\ConnectorRegistry;
use Cobweb\Svconnector\Service\ConnectorBase;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Controller for the backend module
 */
class TestingController extends ActionController
{
    /**
     * @var array List of registered connector services
     */
    protected array $services = [];

    /**
     * @var array List of configuration samples provided by the various connector services
     */
    protected array $sampleConfigurations = [];

    public function __construct()
    {
        $this->services = GeneralUtility::makeInstance(ConnectorRegistry::class)->getAllServices();
        // Get the sample configurations provided by the various connector services
        /** @var ConnectorBase $service */
        foreach ($this->services as $type => $service) {
            $this->sampleConfigurations[$type] = $service->getSampleConfiguration();
        }
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     *
     * @param ViewInterface $view The view to be initialized
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        if ($view instanceof BackendTemplateView) {
            parent::initializeView($view);
            $template = $view->getModuleTemplate();
            $template->getPageRenderer()->addInlineSettingArray(
                'svconnector',
                $this->sampleConfigurations
            );
            $template->getPageRenderer()->loadRequireJsModule('TYPO3/CMS/Svconnector/TestingModule');
        }
    }

    /**
     * Initializes the template to use for all actions.
     *
     * @return void
     */
    protected function initializeAction()
    {
        $this->defaultViewObjectName = BackendTemplateView::class;
    }

    /**
     * Renders the form for testing services.
     *
     * @return HtmlResponse
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \Exception
     */
    public function defaultAction(): HtmlResponse
    {
        $availableServices = [];
        $unAvailableServices = [];

        // Check unavailable services
        // If there are any, display a warning about it and remove it from the list of services
        // All other services are assigned to the view
        $hasUnavailableServices = false;
        /** @var ConnectorBase $service */
        foreach ($this->services as $type => $service) {
            if ($service->isAvailable()) {
                $availableServices[$type] = sprintf(
                    '%s (type: %s)',
                    $service->getName(),
                    $type
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'service.not.available',
                        'svconnector',
                        [
                            get_class($service)
                        ]
                    ),
                    '',
                    AbstractMessage::WARNING
                );
                $unAvailableServices[] = $service;
            }
        }
        $this->view->assign('services', $availableServices);
        if (count($availableServices) === 0) {
            // If all registered services were unavailable, issue a warning
            if (count($unAvailableServices) > 0) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('no.services.available', 'svconnector'),
                    '',
                    AbstractMessage::WARNING
                );

            // If there are simply no registered services, display a notice
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('no.services', 'svconnector'),
                    '',
                    AbstractMessage::NOTICE
                );
            }
        }

        // Check if a request for testing was submitted
        // If yes, execute the testing and pass both arguments and result to the view
        if ($this->request->hasArgument('tx_svconnector')) {
            $arguments = $this->request->getArgument('tx_svconnector');
            // If no parameters were passed, try to fall back on sample configuration, if defined
            if (empty($arguments['parameters'])) {
                $parameters = $this->sampleConfigurations[$arguments['service']] ?? '';
            } else {
                $parameters = $arguments['parameters'];
            }
            $this->view->assignMultiple(
                [
                    'selectedService' => $arguments['service'],
                    'parameters' => $parameters,
                    'format' => $arguments['format'],
                    'testResult' => $this->performTest(
                        $arguments['service'],
                        $arguments['parameters'],
                        (int)$arguments['format']
                    )
                ]
            );
        } else {
            // Select the first service in the list as default and get its sample configuration, if defined
            $defaultService = key($availableServices);
            $defaultParameters = $this->sampleConfigurations[$defaultService] ?? '';
            $this->view->assignMultiple(
                [
                    'selectedService' => $defaultService,
                    'parameters' => $defaultParameters,
                    'format' => 0,
                    'testResult' => ''
                ]
            );
        }

        return new HtmlResponse(
            $this->view->render()
        );
    }

    /**
     * Performs the connection test for the selected service and passes the appropriate results to the view.
     *
     * @param string $type Key of the service to test
     * @param string $parameters Parameters for the service being tested
     * @param int $format Type of format to use (0 = raw, 1 = array, 2 = xml)
     * @return mixed Result from the test
     * @throws \Exception
     */
    protected function performTest(string $type, string $parameters = '', int $format = 0)
    {
        $result = '';

        if (isset($this->services[$type])) {
            $service = $this->services[$type];
            if ($service->isAvailable()) {
                try {
                    $parsedParameters = json_decode($parameters, true, 512, JSON_THROW_ON_ERROR);
                    // Call the right "fetcher" depending on chosen format
                    switch ($format) {
                        case 1:
                            $result = $service->fetchArray($parsedParameters);
                            break;
                        case 2:
                            $result = $service->fetchXML($parsedParameters);
                            break;
                        default:
                            $result = $service->fetchRaw($parsedParameters);
                            break;
                    }
                    // If the result is empty, issue an information message
                    if (empty($result)) {
                        $this->addFlashMessage(
                            LocalizationUtility::translate('no.result', 'svconnector'),
                            '',
                            AbstractMessage::INFO
                        );
                    }
                } // Catch the exception and display an error message
                catch (\Exception $e) {
                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'service.error',
                            'svconnector',
                            [
                                $e->getMessage(),
                                $e->getCode()
                            ]
                        ),
                        '',
                        AbstractMessage::ERROR
                    );
                }
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'service.not.available',
                        'svconnector',
                        [
                            get_class($service)
                        ]
                    ),
                    '',
                    AbstractMessage::ERROR
                );
            }
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'no.service.type',
                    'svconnector',
                    [
                        $type
                    ]
                ),
                '',
                AbstractMessage::ERROR
            );
        }
        return $result;
    }
}
