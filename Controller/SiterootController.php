<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Siteroot controller.
 *
 * @author Phillip Look <plook@brainbits.net>
 * @Route("/siteroots/siteroot")
 * @Security("is_granted('ROLE_SITEROOTS')")
 */
class SiterootController extends Controller
{
    /**
     * List siteroots.
     *
     * @return JsonResponse
     * @Route("/list", name="siteroots_siteroot_list")
     */
    public function listAction()
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroots = [];
        foreach ($siterootManager->findAll() as $siteroot) {
            $siteroots[] = [
                'id' => $siteroot->getId(),
                'title' => $siteroot->getTitle(),
            ];
        }

        return new JsonResponse([
            'siteroots' => $siteroots,
            'count' => count($siteroots),
        ]);
    }

    /**
     * Create siteroot.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="siteroots_siteroot_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title', null);

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = new Siteroot();
        foreach (explode(',', $this->container->getParameter('phlexible_gui.languages.available')) as $language) {
            $siteroot->setTitle($language, $title);
        }
        $siteroot
            ->setCreateUserId($this->getUser()->getId())
            ->setCreatedAt(new \DateTime())
            ->setModifyUserId($siteroot->getCreateUserId())
            ->setModifiedAt($siteroot->getCreatedAt());

        $siterootManager->updateSiteroot($siteroot);

        return new ResultResponse(true, 'New Siteroot created.');
    }

    /**
     * Delete siteroot.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/delete", name="siteroots_siteroot_delete")
     */
    public function deleteAction(Request $request)
    {
        $siterootId = $request->get('id');

        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroot = $siterootManager->find($siterootId);
        $siterootManager->deleteSiteroot($siteroot);

        return new ResultResponse(true, 'Siteroot deleted.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="siteroots_siteroot_load")
     */
    public function loadAction(Request $request)
    {
        $siterootId = $request->get('id');

        $siterootRepository = $this->getDoctrine()->getRepository('PhlexibleSiterootBundle:Siteroot');

        $siteroot = $siterootRepository->find($siterootId);

        $data = [
            'titles' => $siteroot->getTitles(),
            'navigations' => [],
            'properties' => [],
            'specialtids' => [],
            'urls' => [],
        ];

        // get all siteroot navigations
        foreach ($siteroot->getNavigations() as $navigation) {
            $data['navigations'][] = [
                'id' => $navigation->getId(),
                'title' => $navigation->getTitle(),
                'handler' => $navigation->getHandler(),
                'start_tid' => $navigation->getStartTreeId(),
                'max_depth' => $navigation->getMaxDepth(),
                'supports' => '', //call_user_func(array($navigation->getHandler(), 'getSupportedFlags')),
                'flags' => $navigation->getFlags(),
                'additional' => $navigation->getAdditional(),
            ];
        }

        // TODO: siteroot properties from bundles
        /*
        foreach ($componentCallback->getSiterootProperties() as $key) {
            $property = $siteroot->getProperty($key);
            $data['properties'][$key] = strlen($property) ? $property : '';
        }
        */

        foreach ($siteroot->getSpecialTids() as $specialTid) {
            $data['specialtids'][] = [
                'siteroot_id' => $siterootId,
                'key' => $specialTid['name'],
                'language' => !empty($specialTid['language']) ? $specialTid['language'] : null,
                'tid' => $specialTid['treeId'],
            ];
        }

        foreach ($siteroot->getUrls() as $url) {
            $data['urls'][] = [
                'id' => $url->getId(),
                'global_default' => $url->isGlobalDefault(),
                'default' => $url->isDefault(),
                'hostname' => $url->getHostname(),
                'language' => $url->getLanguage(),
                'target' => $url->getTarget(),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Save siteroot.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="siteroots_siteroot_save")
     */
    public function saveAction(Request $request)
    {
        $siterootSaver = $this->get('phlexible_siteroot.siteroot_saver');

        $siterootSaver->saveAction($request);

        return new ResultResponse(true, 'Siteroot data saved');
    }
}
