<?php
namespace App\Controller;

class UsersController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
{
    parent::beforeFilter($event);
    // Configure the login action to not require authentication, preventing
    // the infinite redirect loop issue
    $this->Authentication->addUnauthenticatedActions(['login', 'index', 'register']);}
    

    public function login() {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();
        // regardless of POST or GET, redirect if user is logged in
        if ($result && $result->isValid()) {
            $user = $this->Authentication->getIdentity();
            if ($user->isBanned == 'true') {
                $this->Flash->error(__('Your account is banned.'));
                return $this->redirect(['controller' => 'Users', 'action' => 'logout']);
            } else {
                // redirect to /articles after login success
                return $this->redirect(['controller' => 'Users', 'action' => 'index']);
            }
        }
        // display error if user submitted and authentication failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid username or password'));
        }
    }
    
    

    public function register()
    {
        $usersTable = $this->fetchTable('users');
        $user = $usersTable->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $usersTable->patchEntity($user, $this->request->getData());
            $user->role = 'user';
            if ($usersTable->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['controller' => 'Users', 'action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
    }

    public function logout()
{
    $this->Authentication->logout();
    return $this->redirect(['controller' => 'Users', 'action' => 'index']);
}


public function index() {
    $user = $this->Authentication->getIdentity();
    $this->set('user', $this->Authentication->getIdentity());

    if($user){
       //user is authenticated
       $isAuthenticated = true;
    } else {
       //user is not authenticated
       $isAuthenticated = false;
    }
    

    $petsTable = $this->fetchTable("pets");
    $usersTable = $this->fetchTable('users');


    $users = $usersTable->find()->all();
    $this->set(compact('users'));
    
    $typeNames = $usersTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();
    $this->set('typeNames', $typeNames);
    
    $pets = $petsTable->find()->contain(['Users'])->where(['Pets.users_id = Users.id'])->all();
    $this->set(compact('pets'));
    $totalUsers = $usersTable->find()->count();
    $this->set(compact('totalUsers'));

    

    if ($this->request->is("post")) {
        $petsTable = $this->fetchTable("pets");
        $pets = $petsTable->newEntity($this->request->getData());
        
        // Get the ID of the currently logged-in user
        $user = $this->Authentication->getIdentity();
        $pets->users_id = $user->id;
    
        $pets->createdTime = new \Cake\I18n\FrozenTime();

        if ($petsTable->save($pets)) {
            $this->Flash->success("Pet Has Been Saved");
            return $this->redirect(['action' => 'index']);
        } else {
            $errors = "";
            foreach ($pets->getErrors() as $error) {
                $errors .= " - " . array_values($error)[0] . "<br>";
            }
            $this->Flash->error("Something wrong happened<br>$errors", ['escape' => false]);
        }
    }
}

public function edit($name) {
    // Fetch the pet from the database by name
    $petsTable = $this->fetchTable("pets");
    $pet = $petsTable->find()->where(['name' => $name])->first();

    // Get the ID of the currently logged-in user
    $user = $this->Authentication->getIdentity();
    if ($user->id != $pet->users_id) {
        // The user doesn't own the pet, display an error message or redirect the user
        $this->Flash->error(__('You do not have permission to edit this pet.'));
        return $this->redirect(['controller' => 'Users', 'action' => 'index']);
    }

    if ($this->request->is(['post', 'put'])) {
        $pet = $petsTable->patchEntity($pet, $this->request->getData());
        if ($petsTable->save($pet)) {
            $this->Flash->success("Pet updated successfully.");
            return $this->redirect(['controller' => 'Users', 'action' => 'index']);
        } else {
            $this->Flash->error("An error occurred while trying to update the pet.");
        }
    }
    $this->set(compact('pet'));
}

 
    

public function users() {
    $usersTable = $this->fetchTable("users");
    $users = $usersTable->find()->all();
    
    $loggedInUser = $this->Authentication->getIdentity();
    
    $this->set(compact('users', 'loggedInUser'));
}

public function ban($id) {
    $usersTable = $this->fetchTable("users");
    $user = $usersTable->get($id);
    $user->isBanned = 'true';
    $usersTable->save($user);
    $this->Flash->success("User banned successfully.");
    return $this->redirect(['controller' => 'Users', 'action' => 'users']);
}

    
}
