<?php

namespace Game\Controller;

interface ControllerInterface{
    public function render() : void;
    public function json() : void;
    public function image() : void;
}