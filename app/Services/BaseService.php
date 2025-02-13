<?php

namespace App\Services;

class BaseService {
    protected $respository;

    public function __construct($respository) {
        $this->respository = $respository;
    }

    public function all($pageSize=0){
        return $this->respository->all($pageSize);
    }

    public function find($id){
        return $this->respository->find($id);
    }

    public function save($request){
        $attributes=$request->input();
        return $this->respository->create($attributes);
    }

    public function update($request,$id){
        $attributes=$request->input();
        return $this->respository->update($attributes,$id);
    }

    public function delete($id){
        return $this->respository->delete($id);
    }
}
