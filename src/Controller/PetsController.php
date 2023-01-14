<?php
namespace App\Controller;

class PetsController extends AppController
{
    public function view($name)
    {
        $petsTable = $this->fetchTable("pets");
        $pet = $petsTable->find()->where(['name' => $name])->first();
        $this->set(compact('pet'));
    }

    public function delete($name) {
        // Fetch the pet from the database by name
        $petsTable = $this->fetchTable("pets");
        $pet = $petsTable->find()->where(['name' => $name])->first();
    
        // Get the ID of the currently logged-in user
        $user = $this->Authentication->getIdentity();
        if ($user->id != $pet->users_id && $user->role !== 'admin') {
            // The user doesn't own the pet, display an error message or redirect the user
            $this->Flash->error(__('You do not have permission to delete this pet.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'index']);
        }
    
        // Delete the pet
        $petsTable->delete($pet);
        $this->Flash->success("Pet deleted successfully.");
        return $this->redirect(['controller' => 'Users', 'action' => 'index']);
    }
    
    public function mypets() {
        // Get the ID of the currently logged-in user
        $user = $this->Authentication->getIdentity();
    
        // Fetch the user's pets from the database
        $petsTable = $this->fetchTable("pets");
        $pets = $petsTable->find()
        ->contain(['Users'])
        ->where(['users_id' => $user->id])
        ->all();
        
        // Pass the pets to the view
        $this->set(compact('pets', 'user'));
    }
    
}
