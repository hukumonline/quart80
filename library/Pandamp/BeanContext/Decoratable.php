<?php
/**
 *
 * @package Pandamp_BeanContext
 * @subpackage BeanContext
 * @category BeanContext
 *
 */
interface Pandamp_BeanContext_Decoratable {

   /**
    * Returns the class-name of the entity the class represents
    *
    * @return string
    */
   public function getRepresentedEntity();

   /**
    * Returns an array with all the methods of this class which can be decorated
    * by Pandamp_BeanContext_ModelDecorator
    *
    * @return array
    */
   public function getDecoratableMethods();

}