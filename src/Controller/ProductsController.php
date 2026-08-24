<?php
namespace App\Controller;

class ProductsController extends AppController {

    public function index() {
        $products = $this->Products->find('all', ['order' => 'id DESC']);
        $this->set(compact('products'));
        $this->set('title', 'Products (CakePHP Clone)');
    }

    public function view(?int $id = null) {
        $product = $this->Products->get($id);
        if (!$product) {
            $this->Flash->error('Product not found.');
            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('product'));
        $this->set('title', 'Product: ' . $product->name);
    }

    public function add() {
        $product = $this->Products->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (empty($data['name']) || empty($data['sku']) || ($data['price'] ?? '') === '') {
                $this->Flash->error('Please fill in all required fields (Name, SKU, Price).');
            } else {
                $product = $this->Products->patchEntity($product, $data);
                if ($this->Products->save($product)) {
                    $this->Flash->success('The product has been saved.');
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error('Unable to add the product.');
            }
        }
        $this->set(compact('product'));
        $this->set('title', 'Add Product');
    }

    public function edit(?int $id = null) {
        $product = $this->Products->get($id);
        if (!$product) {
            $this->Flash->error('Product not found.');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            if (empty($data['name']) || empty($data['sku']) || ($data['price'] ?? '') === '') {
                $this->Flash->error('Please fill in all required fields.');
            } else {
                $this->Products->patchEntity($product, $data);
                if ($this->Products->save($product)) {
                    $this->Flash->success('The product has been updated.');
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error('Unable to update the product.');
            }
        }

        $this->set(compact('product'));
        $this->set('title', 'Edit Product: ' . $product->name);
    }

    public function delete(?int $id = null) {
        $product = $this->Products->get($id);
        if ($product) {
            if ($this->Products->delete($product)) {
                $this->Flash->success('The product has been deleted.');
            } else {
                $this->Flash->error('Could not delete the product.');
            }
        } else {
            $this->Flash->error('Product not found.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
