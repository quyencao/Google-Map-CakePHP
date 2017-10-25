<?php
App::uses('AppController', 'Controller');
/**
 * Locations Controller
 *
 * @property Location $Location
 * @property PaginatorComponent $Paginator
 */
class LocationsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */

    public $components = array('Paginator');

    public $paginate = array(
        'limit' => 5,
        'order' => array(
            'Location.created' => 'DESC'
        )
    );

    /**
     * index method
     *
     * @return void
     */
    public function index()
    {
        $this->Paginator->settings = $this->paginate;
        $this->Location->recursive = 0;
        $this->set('locations', $this->Paginator->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null)
    {
        if (!$this->Location->exists($id)) {
            throw new NotFoundException(__('Invalid location'));
        }
        $options = array('conditions' => array('Location.' . $this->Location->primaryKey => $id), 'recursive' => 1);
        $this->set('location', $this->Location->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');
            $this->Location->create();
            if ($this->Location->saveAssociated($this->request->data)) {
                $this->Flash->success(__('Địa điểm đã được lưu lại'));
                $this->response->body(json_encode(array('status' => 'SUCCESS')));
            } else {
//                $this->Flash->error(__('Địa điểm không thể lưu lại. Hãy thử lại'));
                $this->response->body(json_encode(array('status' => 'ERROR')));
            }

            return $this->response;
        }
    }

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Location->exists($id)) {
			throw new NotFoundException(__('Invalid location'));
		}
		if ($this->request->is(array('post', 'put'))) {
		    $this->Location->id = $id;
			if ($this->Location->save($this->request->data)) {
			    $this->Location->Pin->deleteAll(array('location_id' => $id));
                $this->Location->saveAssociated($this->request->data);
				$this->Flash->success(__('Địa điểm ' . $this->request->data['Location']['address'] . ' được cập nhật thành công.'));

				// Return json
                $this->autoRender = false;
                $this->response->type('json');
                $this->response->body(json_encode(array('status' => 'SUCCESS')));
                return $this->response;
			} else {
				$this->Flash->error(__('The location could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Location.' . $this->Location->primaryKey => $id));
			$location = $this->Location->find('first', $options);
			$this->request->data = $location;
			$this->set('location', $location);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Location->id = $id;
		if (!$this->Location->exists()) {
			throw new NotFoundException(__('Invalid location'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Location->delete()) {
			$this->Flash->success(__('Địa điểm đã xóa.'));
		} else {
			$this->Flash->error(__('The location could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
