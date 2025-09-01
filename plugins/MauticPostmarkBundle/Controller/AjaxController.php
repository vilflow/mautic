<?php

namespace MauticPlugin\MauticPostmarkBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as BaseAjaxController;
use MauticPlugin\MauticPostmarkBundle\Service\PostmarkApiService;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends BaseAjaxController
{
    public function getTemplatesAction(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $dataArray = ['success' => 0, 'templates' => []];
        
        $serverToken = trim((string) $request->request->get('server_token', ''));
        
        if (empty($serverToken)) {
            $dataArray['message'] = 'Server token is required';
            return $this->sendJsonResponse($dataArray);
        }

        try {
            // Create the service manually since service locator has limited access
            $postmarkApiService = new PostmarkApiService();
            
            [$success, $templates, $error] = $postmarkApiService->getTemplateList($serverToken);
            
            if (!$success) {
                $dataArray['message'] = $error ?? 'Failed to fetch templates';
                return $this->sendJsonResponse($dataArray);
            }

            $choices = $postmarkApiService->formatTemplateChoices($templates);

            $dataArray['success'] = 1;
            $dataArray['templates'] = $choices;
            $dataArray['message'] = 'Templates fetched successfully';

            return $this->sendJsonResponse($dataArray);

        } catch (\Exception $e) {
            $dataArray['message'] = 'Error fetching templates: ' . $e->getMessage();
            return $this->sendJsonResponse($dataArray);
        }
    }
}