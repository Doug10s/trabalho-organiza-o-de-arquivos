<?php

class Node {
    public $data;
    public $left;
    public $right;
    public $height;

    public function __construct($data)
    {
        $this->data = $data;
        $this->left = null;
        $this->right = null;
        $this->height = 1;
    }
}