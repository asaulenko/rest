<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Movie;
use AppBundle\Entity\Role;
use AppBundle\Exception\ValidationException;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MovieController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Rest\View()
     *
     * @return Movie[]
     */
    public function getMoviesAction()
    {
        $movies = $this->getDoctrine()->getRepository(Movie::class)->findAll();

        return $movies;
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     *
     * @param Movie $movie
     *
     * @param ConstraintViolationListInterface $validationErrors
     * @return Movie
     */
    public function postMoviesAction(Movie $movie, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        return $movie;
    }

    /**
     * @Rest\View()
     *
     * @param Movie|null $movie
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteMovieAction(Movie $movie = null)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();
    }

    /**
     * @Rest\View()
     *
     * @param Movie|null $movie
     *
     * @return Movie|\FOS\RestBundle\View\View
     */
    public function getMovieAction(Movie $movie = null)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        return $movie;
    }

    /**
     * @Rest\View()
     *
     * @param Movie $movie
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMovieRolesAction(Movie $movie)
    {
        return $movie->getRoles();
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("role", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     *
     * @param Movie $movie
     * @param Role $role
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return Role
     */
    public function postMovieRoleAction(Movie $movie, Role $role, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $em = $this->getDoctrine()->getManager();

        $movie->addRole($role);

        $em->persist($role);
        $em->flush();

        return $role;
    }
}
