<?php

namespace App\Controller;

use App\Model\Table\PetsTable;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

class ApiController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function getPetsByUser($userId)
    {
        $petsTable = $this->getTableLocator()->get('Pets');
        $pets = $petsTable->find()->where(['users_id' => $userId])->all();
        $this->set(compact('pets'));
        $this->set('_serialize', 'pets');
    }

    public function deletePet($petId)
    {
        $petsTable = $this->getTableLocator()->get('Pets');
        $pet = $petsTable->get($petId);
        $petsTable->delete($pet);
        $response = new Response();
        $response = $response->withStatus(204);
        return $response;
    }

    public function addPet() {
      $petsTable = $this->fetchTable("pets");
      $pet = $petsTable->newEntity();
      if ($this->request->is('post')) {
          $pet = $petsTable->patchEntity($pet, $this->request->getData());
          $pet->users_id = $this->Auth->user('id');
          if ($petsTable->save($pet)) {
              $this->set(compact('pet'));
              $this->set('_serialize', ['pet']);
          } else {
              throw new BadRequestException("Could not save pet.");
          }
      }
  }
}  