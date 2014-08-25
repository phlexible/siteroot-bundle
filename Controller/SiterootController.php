<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\SiterootsMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Siteroot controller
 *
 * @author Phillip Look <plook@brainbits.net>
 * @Route("/siteroots/siteroot")
 * @Security("is_granted('siteroots')")
 */
class SiterootController extends Controller
{
    /**
     * List siteroots
     *
     * @return JsonResponse
     * @Route("/list", name="siteroots_siteroot_list")
     */
    public function listAction()
    {
        $siterootManager = $this->get('phlexible_siteroot.siteroot_manager');

        $siteroots = array();
        foreach ($siterootManager->findAll() as $siteroot) {
            $siteroots[] = array(
                'id'    => $siteroot->getId(),
                'title' => $siteroot->getTitle(),
            );
        }

        return new JsonResponse(array(
            'siteroots' => $siteroots,
            'count'     => count($siteroots)
        ));
    }

    /**
     * Create siteroot
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
     * Delete siteroot
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
     * Save siteroot
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="siteroots_siteroot_save")
     */
    public function saveAction(Request $request)
    {
        $siterootSaver = $this->get('phlexible_siteroot.siteroot_saver');

        return new ResultResponse(true, 'Siteroot data saved');
    }
}
