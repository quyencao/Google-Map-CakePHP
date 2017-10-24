<?php
App::uses('AppController', 'Controller');
/**
 * Pins Controller
 *
 * @property Pin $Pin
 * @property PaginatorComponent $Paginator
 */
class PinsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Pin->recursive = 0;
		$this->set('pins', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Pin->exists($id)) {
			throw new NotFoundException(__('Invalid pin'));
		}
		$options = array('conditions' => array('Pin.' . $this->Pin->primaryKey => $id));
		$this->set('pin', $this->Pin->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Pin->create();
			if ($this->Pin->save($this->request->data)) {
				$this->Flash->success(__('The pin has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The pin could not be saved. Please, try again.'));
			}
		}
		$locations = $this->Pin->Location->find('list');
		$this->set(compact('locations'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Pin->exists($id)) {
			throw new NotFoundException(__('Invalid pin'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Pin->save($this->request->data)) {
				$this->Flash->success(__('The pin has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The pin could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Pin.' . $this->Pin->primaryKey => $id));
			$this->request->data = $this->Pin->find('first', $options);
		}
		$locations = $this->Pin->Location->find('list');
		$this->set(compact('locations'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Pin->id = $id;
		if (!$this->Pin->exists()) {
			throw new NotFoundException(__('Invalid pin'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Pin->delete()) {
			$this->Flash->success(__('The pin has been deleted.'));
		} else {
			$this->Flash->error(__('The pin could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
