<?php

namespace App\Controller;

use App\Model\Table\PetsTable;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Controller\Component\CsrfComponent;

class ApiController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        //$this->getEventManager()->off($this->Csrf);

        // Configure the login action to not require authentication, preventing
        // the infinite redirect loop issue
       $this->Authentication->addUnauthenticatedActions(['view', 'delete', 'add']);
        //$this->removeMiddleware(AuthenticationMiddleware::class);


    }



        public function view($user_id) {
            $petsTable = $this->fetchTable("pets");
            $pets = $petsTable->find()->where(['users_id' => $user_id])->all();
            if ($pets) {
                $this->set(compact('pets'));
                $this->set('_serialize', ['pets']);
                $this->viewBuilder()->setOption('serialize', ['pets']);
            } else {
                $message = 'Pets not found';
                $this->set(compact('message'));
                $this->set('_serialize', ['message']);
            }
        }
        
        public function delete($id)
{
    $petsTable = $this->fetchTable("pets");
    $pet = $petsTable->get($id);
    if (!$pet) {
        $this->set([
            'message' => 'Pet not found',
            '_serialize' => ['message']
        ]);
        return;
    }
    if ($petsTable->delete($pet)) {
        $this->set([
            'message' => 'Pet deleted',
            '_serialize' => ['message']
        ]);
    } else {
        $this->set([
            'message' => 'Error deleting pet',
            '_serialize' => ['message']
        ]);
    }
}


public function add()
{
    $this->request->allowMethod(['post', 'put']);
    $recipe = $this->Recipes->newEntity($this->request->getData());
    if ($this->Recipes->save($recipe)) {
        $message = 'Saved';
    } else {
        $message = 'Error';
    }
    $this->set([
        'message' => $message,
        'recipe' => $recipe,
    ]);
    $this->viewBuilder()->setOption('serialize', ['recipe', 'message']);
}

}  