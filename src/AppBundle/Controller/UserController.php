<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Route("user")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user, array('roles' => $this->container->getParameter('security.role_hierarchy.roles')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="user_new")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user, array('roles' => $this->container->getParameter('security.role_hierarchy.roles')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse('Utilisateur Ajouté Avec Succès', 200);
        }else{
            return new JsonResponse( str_ireplace('ERROR: ', '',(string) $form->getErrors(true, true) ), 400);
        }
    }

    /**
     * Cange a user entity status.
     *
     * @Route("/changestatus/", name="user_change_statuss")
     * @Route("/changestatus/{id}", name="user_change_status")
     * @Method("POST")
     * @param User|null $user
     * @return JsonResponse
     */
    public function changeStatusAction(User $user = null)
    {
        if($user!= null){
            $user->isEnabled()?$user->setEnabled(false):$user->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush($user);
            return new JsonResponse('Utilisateur a été modifié Avec Succès', 200);
        }else{
            return new JsonResponse( 'Vérifiez vos paramètres', 400);
        }
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/edit/", name="user_edite")
     * @Route("/edit/{id}", name="user_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param User|null $user
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, User $user = null)
    {
        if($user!= null){
            $editForm = $this->createForm('AppBundle\Form\UserType', $user, array('roles' => $this->container->getParameter('security.role_hierarchy.roles')));
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse('Utilisateur a été modifié Avec Succès', 200);
            }elseif($editForm->isSubmitted() && !$editForm->isValid()){
                return new JsonResponse( str_ireplace('ERROR: ', '',(string) $editForm->getErrors(true, true) ), 400);
            }

            return $this->render('user/edit.html.twig', array(
                'user' => $user,
                'form' => $editForm->createView(),
            ));
        }else{
            return new JsonResponse( 'Vérifiez vos paramètres', 400);
        }

    }

    /**
     * Deletes a user entity.
     *
     * @Route("/delete/", name="user_deletee")
     * @Route("/delete/{id}", name="user_delete")
     * @Method("POST")
     * @param Request $request
     * @param User|null $user
     * @return JsonResponse
     */
    public function deleteAction(Request $request, User $user = null)
    {
        if($user!= null){
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            return new JsonResponse('Utilisateur Supprimer Avec Succès', 200);
        }else{
            return new JsonResponse( 'Vérifiez vos paramètres', 400);
        }

    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
