# Zend Framework + Doctrine Form Module

Zend Framework with Doctrine Form Module (Fieldset &amp; InputFilter) usage focusing on re-usable components

###What's this module for?

Every web applications uses a lot of forms. Whether it be for a simple newsletter sign-up, or for more serious matters, such as a registration page or filling out taxes.

For pretty much every project we, the developers, have got to make sure these forms get created and show and that data gets validated and stored. 

This module aims to help developers with the pains of setting up forms by providing the logic for the creation of the forms with the usage of Fieldset and InputFilter classes.

###But... why?

Because creating forms is boring work, so we'd like to do as little of it as possible. 

##Sign me up! What do I need?

Your project must include at least the following requirements:

* PHP 7.2
* Zend Framework 3
* Doctrine ORM Module

##What does the module provide?

This module gives you abstract classes for:

* Form
* Fieldset
* InputFilter
* FormFactory
* FieldsetFactory

A helper Factory is provided for InputFilters, however, creating these is nearly always a custom job (just the once per object though :-) ).

The above listed class types are available for 2 for the 2 supported hydration methods:

* Zend Framework Reflection hydrator
* Doctrine ORM Module DoctrineObject hydrator 

##How to use this module?

Below are 3 examples of implementations provided to get you up and running as soon as possible. 

The examples make use of Doctrine Entity objects, and as such the hydrator. Adjust where you need to use something else. 

The Entity objects and properties/relations used are the following:

 * City 
    * name         (string)
    * Coordinates  (Entity - OneToOne - Required)
    * Address      (Entity - OneToMany - Not required)
 * Address
    * address string (string)
    * Coordinates    (Entity - OneToOne - Not required)
 * Coordinates
    * latitude (string)
    * longitude (string)

The setup above applied to Forms shows re-usability of a Fieldset with InputFilter via the re-use of the required `CoordinatesFieldset`. It also shows (not) required child Fieldsets. Lastly it shows how to use a Collection of Fieldsets for the OneToMany relationship. 

How to use the Collection methodology of the OneToMany relation in Forms also applies for ManyToMany relationships. However, you can only apply this [on the owning side](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/unitofwork-associations.html) of a relationship. 

[Clone this repo for the example code](https://github.com/rkeet/zf-doctrine-form-examples)

TODO - fill in the docs below

###Basic form
###Form using a Fieldset
###Form using a Collection of Fieldsets
