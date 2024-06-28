<?php

namespace App\Controller;

use App\Demo;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement;
use Twig\Environment;

#[Route("/admin/recettes", name: 'admin.recipe.')]
class RecipeController extends AbstractController
{

    
    #[Route('/', name: 'index')]
    public function index(RecipeRepository $repository): Response
    {
        
        $recipe = $repository->findAll();
        return $this->render('recipe/index.html.twig', ['recipes' => $recipe]);
    }


    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Recipe $recipe,
        Request $request, 
        EntityManagerInterface $em, 
        FormFactoryInterface $formFactory,
        ){
        $form = $formFactory->create(RecipeType::class, $recipe);
        $form->handleRequest($request);
      
        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'La recette a bien ete modifier');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/store.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
        
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em){
        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien ete ajouter');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/store.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em) 
    {
        
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette est supprimer');
        return $this->redirectToRoute('recipe.index');
    }
}
