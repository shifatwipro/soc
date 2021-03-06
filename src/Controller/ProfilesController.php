<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Profiles Controller
 *
 * @property \App\Model\Table\ProfilesTable $Profiles
 *
 * @method \App\Model\Entity\Profile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProfilesController extends AppController
{


    public function initialize()
    {
        parent::initialize();
        // Add the action to the allowed actions list.
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $userPerm = $this->getUserAssignedPermissions('index_profile');
        $this->paginate = [
            'contain' => ['Users']
        ];
        $profiles = $this->paginate($this->Profiles);
        $users = $this->Profiles->Users->find('list', ['limit' => 200]);
        $this->set(compact('profiles','users'));
    }

    //My profile edit add
    public function indexmy()
    {
        $userPerm = $this->getUserAssignedPermissions('indexmy_profile');
        $this->paginate = [
            'contain' => ['Users']
        ];
        $profiles = $this->paginate($this->Profiles->find('all')->where(['user_id'=> $this->Auth->user()['id']]));

        $this->set(compact('profiles'));
    }

    /**
     * View method
     *
     * @param string|null $id Profile id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $profile = $this->Profiles->get($id, [
            'contain' => ['Users']
        ]);

        $this->set('profile', $profile);
    }

    public function viewmy($id = null)
    {
        $profile = $this->Profiles->get($id, [
            'contain' => ['Users']
        ]);

        $this->set('profile', $profile);
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $profile = $this->Profiles->newEntity();
        if ($this->request->is('post')) {
            $dir = \Cake\Core\Configure::read('App.wwwRoot');
            $upLoadsDirectory = $dir.'/img/profile';

            if (!file_exists($upLoadsDirectory)) {
                mkdir($upLoadsDirectory, 0777, true);
            }

            //for picture
            $fileParams = $this->request->data['images'];
            $info = pathinfo($fileParams['name']);
            $pathPicture = md5($fileParams['name']) . '-' . uniqid() . '.' . $info['extension'];
            if (!move_uploaded_file($this->request->data['images']['tmp_name'], $upLoadsDirectory.'/' . $pathPicture)) {
                var_dump('Cant move picture ');
                die;
            }
            unset($this->request->data['images']);
            //end
            //start signature
            $fileParams = $this->request->data['sgn'];
            $info = pathinfo($fileParams['name']);
            $pathSign = md5($fileParams['name']) . '-' . uniqid() . '.' . $info['extension'];
            if (!move_uploaded_file($this->request->data['sgn']['tmp_name'], $upLoadsDirectory.'/' . $pathSign)) {
                var_dump('Cant move signature ');
                die;
            }
            unset($this->request->data['sgn']);
            //end
            $profile = $this->Profiles->patchEntity($profile, $this->request->getData());
            $profile->picture = $pathPicture;
            $profile->sgn = $pathSign;
            if ($this->Profiles->save($profile)) {
                $this->Flash->success(__('The profile has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }
        $status =array(""=>"Select","1"=>"Approved","2"=>"Not Approved");
        $users = $this->Profiles->Users->find('list', ['limit' => 200]);
        $this->set(compact('profile', 'users','status'));
    }

    public function addmy()
    {
        $profile = $this->Profiles->newEntity();
        if ($this->request->is('post')) {
            $dir = \Cake\Core\Configure::read('App.wwwRoot');
            $upLoadsDirectory = $dir.'/img/profile';

            if (!file_exists($upLoadsDirectory)) {
                mkdir($upLoadsDirectory, 0777, true);
            }

            //for picture
            $fileParams = $this->request->data['images'];
            $info = pathinfo($fileParams['name']);
            $pathPicture = md5($fileParams['name']) . '-' . uniqid() . '.' . $info['extension'];
            if (!move_uploaded_file($this->request->data['images']['tmp_name'], $upLoadsDirectory.'/' . $pathPicture)) {
                var_dump('Cant move picture ');
                die;
            }
            unset($this->request->data['images']);
            //end
            //start signature
            $fileParams = $this->request->data['sgn'];
            $info = pathinfo($fileParams['name']);
            $pathSign = md5($fileParams['name']) . '-' . uniqid() . '.' . $info['extension'];
            if (!move_uploaded_file($this->request->data['sgn']['tmp_name'], $upLoadsDirectory.'/' . $pathSign)) {
                var_dump('Cant move signature ');
                die;
            }
            unset($this->request->data['sgn']);
            //end
            $profile = $this->Profiles->patchEntity($profile, $this->request->getData());
            $profile->user_id = $this->_userId();
            $profile->picture = $pathPicture;
            $profile->sgn = $pathSign;
            $profile->status = 2;
            if ($this->Profiles->save($profile)) {
                $this->Flash->success(__('The profile has been saved.'));

                return $this->redirect(['action' => 'indexmy']);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }
        $users = TableRegistry::get('Users')->find('all')->where(['id'=>$this->_userId()])->first();

        $this->set(compact('profile', 'users'));
    }
    /**
     * Edit method
     *
     * @param string|null $id Profile id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $profile = $this->Profiles->get($id, [
            'contain' => ['Users']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            // copy paste
            $dir = \Cake\Core\Configure::read('App.wwwRoot');
            $upLoadsDirectory = $dir.'/img/profile';

            if (!file_exists($upLoadsDirectory)) {
                mkdir($upLoadsDirectory, 0777, true);
            }
            //end :)
            //for picture
            $fileParams = $this->request->data['images'];
            $info = pathinfo($fileParams['name']);
            $pathPicture = md5($fileParams['name']) . '-' . uniqid() . '.' . $info['extension'];

            if(!empty($pathPicture) && !empty($this->request->data['images']['name'])){
                $imageTrue ='ok';
                if (!move_uploaded_file($this->request->data['images']['tmp_name'], $upLoadsDirectory.'/' . $pathPicture)) {
                    var_dump('Cant move picture ');
                    die;
                }
            }

            unset($this->request->data['images']);
            //end
            //start signature
            $fileParams = $this->request->data['sgn'];
            $info = pathinfo($fileParams['name']);
            $pathSign = md5($fileParams['name']) . '-' . uniqid() . '.' . $info['extension'];

            if(!empty($pathSign) && !empty($this->request->data['sgn']['name'])){
                $signTrue ='ok';
            if (!move_uploaded_file($this->request->data['sgn']['tmp_name'], $upLoadsDirectory.'/' . $pathSign)) {
                var_dump('Cant move signature ');
                die;
            }
            }
            unset($this->request->data['sgn']);
            //end

            $profile = $this->Profiles->patchEntity($profile, $this->request->getData());

            if(!empty($pathPicture) && !empty($imageTrue)){$profile->picture = $pathPicture;}

            if(!empty($pathSign) && !empty($signTrue)){$profile->sgn = $pathSign;}

            if ($this->Profiles->save($profile)) {
                $this->Flash->success(__('The profile has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }
        $status =array(""=>"Select","1"=>"Approved","2"=>"Not Approved");
        $users = $this->Profiles->Users->find('list', ['limit' => 200]);
        $this->set(compact('profile', 'users','status'));
    }

    public function editmy($id = null)
    {
        $profile = $this->Profiles->get($id, [
            'contain' => ['Users']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            // copy paste
            $dir = \Cake\Core\Configure::read('App.wwwRoot');
            $upLoadsDirectory = $dir.'/img/profile';

            if (!file_exists($upLoadsDirectory)) {
                mkdir($upLoadsDirectory, 0777, true);
            }
            //end :)
            //for picture
            $fileParams = $this->request->data['images'];
            $info = pathinfo($fileParams['name']);
            $pathPicture = md5($fileParams['name']) . '-' . uniqid() . '.' . $info['extension'];

            if(!empty($pathPicture) && !empty($this->request->data['images']['name'])){
                $imageTrue ='ok';
                if (!move_uploaded_file($this->request->data['images']['tmp_name'], $upLoadsDirectory.'/' . $pathPicture)) {
                    var_dump('Cant move picture ');
                    die;
                }
            }

            unset($this->request->data['images']);
            //end
            //start signature
            $fileParams = $this->request->data['sgn'];
            $info = pathinfo($fileParams['name']);
            $pathSign = md5($fileParams['name']) . '-' . uniqid() . '.' . $info['extension'];

            if(!empty($pathSign) && !empty($this->request->data['sgn']['name'])){
                $signTrue ='ok';
                if (!move_uploaded_file($this->request->data['sgn']['tmp_name'], $upLoadsDirectory.'/' . $pathSign)) {
                    var_dump('Cant move signature ');
                    die;
                }
            }
            unset($this->request->data['sgn']);
            //end

            $profile = $this->Profiles->patchEntity($profile, $this->request->getData());

            if(!empty($pathPicture) && !empty($imageTrue)){$profile->picture = $pathPicture;}

            if(!empty($pathSign) && !empty($signTrue)){$profile->sgn = $pathSign;}
            $profile->user_id = $this->_userId();
            if ($this->Profiles->save($profile)) {
                $this->Flash->success(__('The profile has been saved.'));

                return $this->redirect(['action' => 'indexmy']);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }
        $users = $this->Profiles->Users->find('list', ['limit' => 200]);
        $this->set(compact('profile', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Profile id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $profile = $this->Profiles->get($id);
        //if $profile not found, redirect back to list view
        if (empty($profile)) {
            $this->Flash->error(__('User not found'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->Profiles->delete($profile)) {
            return $this->response->withType("application/json")->withStringBody(json_encode(array('status' => 'deleted'))); die;
        } else {
            return $this->response->withType("application/json")->withStringBody(json_encode(array('status' => 'error'))); die;
        }
        return $this->redirect(['action' => 'index']);
    }
}
