<?php

namespace App\Controller;

use App\Entity\Faction;
use App\Repository\FactionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FactionController extends AbstractController
{
    public function __construct(private UserRepository $userRepo,private FactionRepository $factionRepo) {}

    #[Route('/faction/create',  name: 'app_faction_create',methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
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

        if ($user->getCredits() < 1000) {
            return $this->json(["message" => "Pas assez de crédits"]);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return $this->json(["message" => "Nom de faction requis"]);
        }

        $faction = new Faction();
        $faction->setName($data['name']);
        $faction->setChef($user);

        $user->setCredits($user->getCredits() - 1000);
        $user->setFaction($faction);

        $em->persist($faction);
        $em->flush();

        return $this->json(["message" => "Faction créée"]);
    }

    #[Route('/factions/list', name: 'app_faction_list', methods: ['GET'])]
    public function list(): Response
    {
        $factions = $this->factionRepo->findAll();
        $result = [];

        foreach ($factions as $faction) {
            $result[] = [
                "id" => $faction->getId(),
                "name" => $faction->getName(),
                "chef" => $faction->getChef()->getPseudoMincraft()
            ];
        }

        return $this->json($result);
    }

    #[Route('/faction/join/{id}', name: 'app_faction/join', methods: ['POST'])]
    public function join($id, Request $request, EntityManagerInterface $em): Response
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

        $faction = $this->factionRepo->find($id);

        if (!$faction) {
            return $this->json(["message" => "Faction introuvable"]);
        }

        $user->setFaction($faction);
        $em->flush();

        return $this->json(["message" => "Faction rejointe"]);
    }

    #[Route('/faction/delete/{id}', name: 'app_faction_delete', methods: ['DELETE'])]
    public function delete($id, Request $request, EntityManagerInterface $em): Response
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

        $faction = $this->factionRepo->find($id);

        if (!$faction) {
            return $this->json(["message" => "Faction introuvable"]);
        }

        if ($faction->getChef() !== $user && !in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->json(["message" => "Accès refusé"]);
        }

        $em->remove($faction);
        $em->flush();

        return $this->json(["message" => "Faction supprimée"]);
    }
}
