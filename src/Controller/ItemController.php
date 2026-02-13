<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends AbstractController
{
    public function __construct(private UserRepository $userRepo,private ItemRepository $itemRepo){}

    #[Route('/shop',name: 'app_shop', methods: ['GET'])]
    public function shop(): Response
    {
        $items = $this->itemRepo->findAll();
        $result = [];

        foreach ($items as $item) {
            $result[] = [
                "id" => $item->getId(),
                "Nom" => $item->getNom(),
                "Description" => $item->getDescription(),
                "Prix" => $item->getPrix(),
                "Rareté" => $item->getRarete()
            ];
        }

        return $this->json($result);
    }

    #[Route('/shop/buy/{id}',name: 'app_shop-buy', methods: ['POST'])]
    public function buy($id, Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->headers->get('Authorization');

        if (!$token) {
            return $this->json(["message" => "Token manquant"]);
        }

        $token = substr($token, 7);
        $user = $this->userRepo->findOneBy(["token" => $token]);

        if (!$user) {
            return $this->json(["message" => "Utilisateur introuvable"]);
        }

        $item = $this->itemRepo->find($id);

        if (!$item) {
            return $this->json(["message" => "Objet introuvable"]);
        }

        if ($user->getCredits() < $item->getPrice()) {
            return $this->json(["message" => "Pas assez de crédits"]);
        }

        $user->setCredits($user->getCredits() - $item->getPrice());

        $user->addItem($item);

        $em->flush();

        return $this->json([
            "message" => "Objet acheté",
            "CreditsRestants" => $user->getCredits()
        ]);
    }


    #[Route('/inventaire',name: 'app_inventaire', methods: ['GET'])]
    public function inventory(Request $request): Response
    {
        $token = $request->headers->get('Authorization');

        if (!$token) {
            return $this->json(["message" => "Token manquant"]);
        }

        $token = substr($token, 7);
        $user = $this->userRepo->findOneBy(["token" => $token]);

        if (!$user) {
            return $this->json(["message" => "Utilisateur introuvable"]);
        }

        $items = $user->getItems();
        $result = [];

        foreach ($items as $item) {
            $result[] = [
                "id" => $item->getId(),
                "Nom" => $item->getNom(),
                "Rareté" => $item->getRarete()
            ];
        }

        return $this->json([
            "Credits" => $user->getCredits(),
            "inventaire" => $result
        ]);
    }
}
