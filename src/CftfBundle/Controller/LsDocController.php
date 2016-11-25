<?php

namespace CftfBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CftfBundle\Entity\LsDoc;
use CftfBundle\Form\LsDocType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * LsDoc controller.
 *
 * @Route("/lsdoc")
 */
class LsDocController extends Controller
{
    /**
     * Lists all LsDoc entities.
     *
     * @Route("/", name="lsdoc_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lsDocs = $em->getRepository('CftfBundle:LsDoc')->findAll();

        return [
            'lsDocs' => $lsDocs,
        ];
    }

    /**
     * Creates a new LsDoc entity.
     *
     * @Route("/new", name="lsdoc_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @Security("is_granted('create', 'lsdoc')")
     */
    public function newAction(Request $request)
    {
        $lsDoc = new LsDoc();
        $form = $this->createForm('CftfBundle\Form\LsDocType', $lsDoc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lsDoc);
            $em->flush();

            return $this->redirectToRoute('lsdoc_show', array('id' => $lsDoc->getId()));
        }

        return [
            'lsDoc' => $lsDoc,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a LsDoc entity.
     *
     * @Route("/{id}.{_format}", defaults={"_format"="html"}, name="lsdoc_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(LsDoc $lsDoc, $_format = 'html')
    {
        if ('json' === $_format) {
            // Redirect?  Change Action for Template?
            return [ 'lsDoc' => $lsDoc ];
        }

        $deleteForm = $this->createDeleteForm($lsDoc);

        return [
            'lsDoc' => $lsDoc,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing LsDoc entity.
     *
     * @Route("/{id}/edit", name="lsdoc_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @Security("is_granted('edit', lsDoc)")
     */
    public function editAction(Request $request, LsDoc $lsDoc)
    {
        $ajax = false;
        if ($request->isXmlHttpRequest()) {
            $ajax = true;
        }

        $deleteForm = $this->createDeleteForm($lsDoc);
        $editForm = $this->createForm(LsDocType::class, $lsDoc, ['ajax' => $ajax]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lsDoc);
            $em->flush();

            if ($ajax) {
                return new Response('OK', Response::HTTP_ACCEPTED);
            }

            return $this->redirectToRoute('lsdoc_edit', array('id' => $lsDoc->getId()));
        }

        $ret = [
            'lsDoc' => $lsDoc,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];

        if ($ajax && $editForm->isSubmitted() && !$editForm->isValid()) {
            return $this->render('CftfBundle:LsDoc:edit.html.twig', $ret, new Response('', Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        return $ret;
    }

    /**
     * Deletes a LsDoc entity.
     *
     * @Route("/{id}", name="lsdoc_delete")
     * @Method("DELETE")
     * @Security("is_granted('edit', lsDoc)")
     */
    public function deleteAction(Request $request, LsDoc $lsDoc)
    {
        $form = $this->createDeleteForm($lsDoc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lsDoc);
            $em->flush();
        }

        return $this->redirectToRoute('lsdoc_index');
    }

    /**
     * Creates a form to delete a LsDoc entity.
     *
     * @param LsDoc $lsDoc The LsDoc entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LsDoc $lsDoc)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lsdoc_delete', array('id' => $lsDoc->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Finds and displays a LsDoc entity.
     *
     * @ApiDoc(
     *   resource=true,
     *   resourceDescription="Operations on LsDoc",
     *   description="Get an LsDoc",
     *   https=true,
     *   tags={"beta"="#ff0000"},
     *   section="cftf",
     *   requirements={
     *     {
     *       "name"="id",
     *       "dataType"="string",
     *       "description"="Identifier of the LsDoc"
     *     },
     *     {
     *       "name"="_format",
     *       "dataType"="string",
     *       "requirement"="json|html|null",
     *       "description"="Short mime type of output, defaults to json if not provided",
     *       "default"="json",
     *       "required"=false
     *     }
     *   },
     *   input="LsDoc",
     *   output="\CftfBundle\Entity\LsDoc"
     * )
     *
     * @Route("/{id}/export.{_format}", requirements={"_format"="(json|html|null)"}, defaults={"_format"="json"}, name="lsdoc_export")
     * @Method("GET")
     * @Template()
     */
    public function exportAction(LsDoc $lsDoc, $_format = 'json')
    {
        $items = $this->getDoctrine()->getRepository('CftfBundle:LsDoc')->findAllChildrenArray($lsDoc);

        return [
            'lsDoc' => $lsDoc,
            'items' => $items,
        ];
    }
}