# Zend Framework + Doctrine Form Module

Zend Framework with Doctrine Form Module (Fieldset &amp; InputFilter) usage focusing on re-usable components

### What's this module for?

Every web applications uses a lot of forms. Whether it be for a simple newsletter sign-up, or for more serious matters, 
such as a registration page or filling out taxes.

For pretty much every project we, the developers, have got to make sure these forms get created and show and that data 
gets validated and stored. 

This module aims to help developers with the pains of setting up forms by providing the logic for the creation of the 
forms with the usage of Fieldset and InputFilter classes.

### But... why?

Because creating forms is boring work, so we'd like to do as little of it as possible. 

## Sign me up! What do I need?

Your project must include at least the following requirements:

* PHP 7.2
* Zend Framework 3
* Doctrine ORM Module

## What does the module provide?

This module gives you abstract classes for:

* Form
* Fieldset
* InputFilter
* FormFactory
* FieldsetFactory

A helper Factory is provided for InputFilters, however, creating these is nearly always a custom job (just the once per 
object though :-) ).

The above listed class types are available for 2 for the 2 supported hydration methods:

* Zend Framework **Reflection hydrator**
* Doctrine ORM Module **DoctrineObject hydrator**

## How to use this module?

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

The setup above applied to Forms shows re-usability of a Fieldset with InputFilter via the re-use of the required 
`CoordinatesFieldset`. It also shows (not) required child Fieldsets. Lastly it shows how to use a Collection of 
Fieldsets for the OneToMany relationship. 

How to use the Collection methodology of the OneToMany relation in Forms also applies for ManyToMany relationships. 
However, you can only apply this 
[on the owning side](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/unitofwork-associations.html) 
of a relationship. 

[Clone this repo for the example code](https://github.com/rkeet/zf-doctrine-form-examples) (examples below from that 
repo)

## Some information before we dive in. !! Important you understand !!

* A Form class **should always be for a single purpose**! Purpose is not to be confused with object. 
* You should manage only a **single object per Fieldset**! 
* *You may have more than 1 Fieldset per Form*. 
* You can have as many Fieldset's in a Form as you like. 
* A Form is, however, **always singular**! You should not, and cannot, nest HTML Form objects. As such, this is also 
*not supported by Zend Framework*.

# Form example
## Basic form

From now on, all of your forms will look like this!

```php
class CoordinatesForm extends AbstractDoctrineForm
{
    public function init()
    {
        $this->add([
            'name' => 'coordinates',
            'type' => CoordinatesFieldset::class,
            'options' => [
                'use_as_base_fieldset' => true,
            ],
        ]);

        //Call parent initializer. Check in parent what it does.
        parent::init();
    }
}
``` 

Your Form objects will now always look like this, because all of your logic and requirements will be spread among the
Fieldset and InputFilter. 

# Fieldset examples
## Form using a Fieldset

A Fieldset will look like this.

```php
class CoordinatesFieldset extends AbstractDoctrineFieldset
{
    public function init()
    {
        parent::init();

        $this->add([
            'name' => 'latitude',
            'required' => true,
            'type' => Text::class,
            'options' => [
                'label' => _('Latitude'),
            ],
        ]);
        
        // ... Add longitude
    }
}
```

The magic is that we define only the fields within a Fieldset. **Never again use `getInputFilterSpecification`**!!!

You you've ever made something using [Separation of Concerns](https://en.wikipedia.org/wiki/Separation_of_concerns), 
then using that function should've already made you shiver. Now, here you're getting another way of doing things.

## A Fieldset using another Fieldset

*A Fieldset using a Fieldset means a OneToOne relation*

```php
class AddressFieldset extends AbstractDoctrineFieldset
{
    public function init()
    {
        parent::init();
        
        // ... add street
        // ... add city dropdown selector (based on available City Entity objects)

        $this->add([
            'type' => CoordinatesFieldset::class,
            'required' => false,
            'name' => 'coordinates',
            'options' => [
                'label' => _('Coordinates'),
                'use_as_base_fieldset' => false,
            ],
        ]);
    }
}
```

As you can see here, we're creating an Input, but setting another Fieldset as being the Type. This is perfectly valid
according to Zend Framework. Zend Framework goes even further by recognising this as being a child Fieldset, as it
recognises it for what it is. This is normally, incorrectly, called a "child form". It's obviously a child Fieldset,
which when rendered also creates valid HTML. 

## A Fieldset using a Collection of a Fieldset

*A Fieldset using a Collection of a Fieldset means a OneToMany or ManyToMany relation!*

```php
class CityFieldset extends AbstractDoctrineFieldset
{
    /**
     * @var AddressFieldset
     */
    protected $addressFieldset;

    public function __construct(
        ObjectManager $objectManager,
        string $name,
        array $options = [],
        AddressFieldset $addressFieldset
    ) {
        $this->setAddressFieldset($addressFieldset);

        parent::__construct($objectManager, $name, $options);
    }

    public function init()
    {
        parent::init();

        // ... add name

        $this->add([
            'type' => Collection::class,
            'required' => false,
            'name' => 'addresses',
            'options' => [
                'label' => _('Addresses'),
                'count' => 1,
                'allow_add' => true,
                'allow_remove' => true,
                'should_create_template' => true,
                'target_element' => $this->getAddressFieldset(),
            ],
        ]);

        // ... add coordinates fieldset
    }
    
    // ... getter/setter for $addressFieldset
}
```

Here we get to the real magic. If you read this carefully, we see that here we do not set the type to being that of a 
Fieldset, like we do with a OneToOne relation setup. Here we set the property type to Collection. A Collection 
Element also has a number of different option properties, such as `count`, `allow_add/remove`, `should_create_template` and
`target_element`. 

* `count` indicates the amount Fieldsets to render of the set `target_element`
* `allow_add/remove` gives us the option to allow the user to decide whether to add new objects with new Fieldsets or 
to remove existing ones 
* `should_create_template`, if set to `true`, creates a hidden `<span>` element containing the entire Fieldset, rendered,
in an HTML attribute. This allows you to use JavaScript client side to create/remove Fieldsets without AJAX. 
* `target_element`, in contrast to the OneToOne above, contains a fully created Fieldset object. This must be provided
here as the value, it cannot just be a FQCN or alias to the required Fieldset. 

# InputFilter examples

## Basic Form InputFilter

```php
class CoordinatesFormInputFilter extends AbstractDoctrineFormInputFilter
{
    /** @var CoordinatesFieldsetInputFilter  */
    protected $coordinatesFieldsetInputFilter;

    public function __construct(
        ObjectManager $objectManager,
        Translator $translator,
        CoordinatesFieldsetInputFilter $filter
    ) {
        $this->coordinatesFieldsetInputFilter = $filter;

        parent::__construct([
            'object_manager' => $objectManager,
            'translator' => $translator,
        ]);
    }

    public function init()
    {
        $this->add($this->coordinatesFieldsetInputFilter, 'coordinates');

        parent::init();
    }
}
```

Your Form InputFilter classes will from now on closely resemble the above. The provided 
`AbstractDoctrineFormInputFilter` provides CSRF validation, so all you need to do is call `parent::init()`.

**You must make sure that you match the name of the FieldsetInputFilter to be the same as the name you used for the
Fieldset in the Form class!!!**

These names **must** match! Zend Framework uses names given to Fieldsets and InputFilters to match which 2 belong 
together. As such, **very important!**

## Basic Fieldset InputFilter

*Basic InputFilter does not include another InputFilter*

```php
class CoordinatesFieldsetInputFilter extends AbstractDoctrineFieldsetInputFilter
{
    public function init()
    {
        parent::init();

        $this->add([
            'name' => 'latitude',
            'required' => true,
            'allow_empty' => true,
            'filters' => [
                ['name' => StringTrim::class],
                ['name' => StripTags::class],
                [
                    'name' => ToNull::class,
                    'options' => [
                        'type' => ToNull::TYPE_STRING,
                    ],
                ],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 255,
                    ],
                ],
            ],
        ]);

        // ... validate longitude
    }
}
```

Your basic InputFilter will now look like this. It contains, in extremely similar fashion to the Fieldset, the Filter 
and Validator counterparts to the Fieldset Element objects. 

**Tip:** for every Element you validate, add the `ToNull` filter (and use the correct `type` in its options). If you 
allow an Entity property to be `null`, this causes an empty form field to become `null` instead of an empty string 
(`''`).

## InputFilter using another InputFilter

*An InputFilter using another InputFilter is for a One To One relation*

```php

class AddressFieldsetInputFilter extends AbstractDoctrineFieldsetInputFilter
{
    /** @var CoordinatesFieldsetInputFilter $coordinatesFieldsetInputFilter */
    protected $coordinatesFieldsetInputFilter;

    public function __construct(
        ObjectManager $objectManager,
        Translator $translator,
        CoordinatesFieldsetInputFilter $filter
    ) {
        $this->coordinatesFieldsetInputFilter = $filter;

        parent::__construct([
            'object_manager' => $objectManager,
            'object_repository' => $objectManager->getRepository(Address::class),
            'translator' => $translator,
        ]);
    }

    public function init()
    {
        parent::init();

        $this->add($this->coordinatesFieldsetInputFilter, 'coordinates');

        // ... validate street

        // ... city can be validated like this because it's a `Select` element, not a child Fieldset
        $this->add([
            'name' => 'city',
            'required' => true,
        ]);
    }
}
```

An Address *may* have Coordinates. This is a OneToOne relation where the AddressFieldset references a 
CoordinatesFieldset as the type of one of its Element objects. As such, we need to make sure to use the 
CoordinatesFieldsetInputFilter in the AddressFieldsetInputFilter. 

**IT'S VERY IMPORTANT TO MATCH THE FIELDSET AND INPUT FILTER STRUCTURES SO THEY'RE EXACTLY THE SAME!!!**

Like with the names (of the Fieldsets and Input Filters) mentioned above, this is **very important**. Zend Framework
matches the structures as it does the names to make sure they belong together.

If you mess up the structures, you'll end up with Fieldsets being validated to the defaults of the Input Elements
they're made out of. This may work, but most of the time it will fail. 

To achieve the above, that you can add the CoordinatesFieldset, you must pass it along to the class from a Factory. 

The configuration so you can use Zend Framework Managers to get the correct objects is shown later.

## InputFilter using a Collection of another InputFilter

Within the InputFilter class this is very much the same as the above. However, notice the `type` for the Address 
InputFilter class. It's of the type `CollectionInputFilter` instead of extended from `AbstractInputFilter`. 

```php
class CityFieldsetInputFilter extends AbstractDoctrineFieldsetInputFilter
{
    /** @var CollectionInputFilter $addressFieldsetCollectionInputFilter */
    protected $addressFieldsetCollectionInputFilter;

    /** @var CoordinatesFieldsetInputFilter $coordinatesFieldsetInputFilter */
    protected $coordinatesFieldsetInputFilter;

    public function __construct(
        ObjectManager $objectManager,
        Translator $translator,
        CollectionInputFilter $addressFieldsetCollectionInputFilter,
        CoordinatesFieldsetInputFilter $coordinatesFieldsetInputFilter
    ) {
        $this->addressFieldsetCollectionInputFilter = $addressFieldsetCollectionInputFilter;
        $this->coordinatesFieldsetInputFilter = $coordinatesFieldsetInputFilter;

        parent::__construct([
            'object_manager' => $objectManager,
            'object_repository' => $objectManager->getRepository(City::class),
            'translator' => $translator,
        ]);
    }

    public function init()
    {
        parent::init();

        $this->add($this->addressFieldsetCollectionInputFilter, 'addresses');
        $this->add($this->coordinatesFieldsetInputFilter, 'coordinates');

        // ... validate name
    }
}
```

# Using Factories

## Creating a basic Form

```php
class CoordinatesFormFactory extends AbstractDoctrineFormFactory
{
    public function __construct()
    {
        parent::__construct(CoordinatesForm::class, CoordinatesFormInputFilter::class);
    }
}
```

Yea... That's it. Earlier it was mentioned that you should only **have a single purpose** to a Form. As such, a Form
will have a single `base_fieldset`. Because you're creating one Form, you need one FormInputFilter. So yea... That's it.

## Creating a basic Fieldset

```php
class CoordinatesFieldsetFactory extends AbstractDoctrineFieldsetFactory
{
    public function __construct()
    {
        // params, in order: FQCN of Fieldset, name of Fieldset, Object/Entity FQCN for Hydrator
        parent::__construct(CoordinatesFieldset::class, 'coordinates', Coordinates::class);
    }
}
```

For normal Fieldset's and for Fieldsets containing a One To One relationship, you can use the above example. 

## Creating a Fieldset with a Collection

```php
class CityFieldsetFactory extends AbstractDoctrineFieldsetFactory
{
    // This function as normal
    public function __construct()
    {
        parent::__construct(CityFieldset::class, 'city', City::class);
    }

    // To use a Collection you must override the __invoke method. Example below

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CityFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setEntityManager($container->get(EntityManager::class));
        $this->setTranslator($container->get('MvcTranslator'));

        /** @var FormElementManagerV3Polyfill $formManager */
        $formManager = $container->get('FormElementManager');
        /** @var AddressFieldset $addressFieldset */
        $addressFieldset = $formManager->get(AddressFieldset::class);
        $addressFieldset->remove('city'); // Already being populated with Entity being created in base_fieldset

        /** @var CityFieldset $fieldset */
        $fieldset = new CityFieldset($this->getEntityManager(), 'city', [], $addressFieldset);
        $fieldset->setHydrator(
            new DoctrineObject($this->getEntityManager())
        );
        $fieldset->setObject(new City());
        $fieldset->setTranslator($this->getTranslator());

        return $fieldset;
    }
}
```

To create what you need for a Collection, you have to override the `__invoke` function. A function which could receive 
additional config to add `Collection` objects to a Form *could've* been created. It was decided against because the 
implementation would usually vary enough that you end up with nearly the amount of code in the example above anyway.

Note the line: `$addressFieldset->remove('city');`. 

If you're using Doctrine, you'll most often use bi-directional relationships. The Doctrine Hydrator is smart. Very smart.
Because of this structure, it recognises that the objects you have in your Form are related to one another. This is why
we make sure that Fieldset Element names match **exactly** (capital sensitive!) to the Entity property names.

If you get that right, the Doctrine Hydrator automagically creates the entire related structure of Entity objects, based
upon the Form's structure. Seriously, check the Controller and Entity functionality in the example module if you don't 
believe me. 

The Doctrine Hydrator's smartness is also its pitfall. Because it knows your structure you must make sure to *not create
an endless looping structure*.

The code example above shows you the `CityFieldset` creation. The `City#address` property is configured to allow the 
creation of one or more Address Entity objects. Vice versa, the `Address#city` property is configured to allow the 
creation of a City object. As such, make sure you remove the "self" creator from the "counter Fieldset" class when you
create the Fieldset. 

## Create a Basic FieldsetInputFilter

```php
class CoordinatesFieldsetInputFilterFactory extends AbstractDoctrineFieldsetInputFilterFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        parent::setupRequirements($container, Coordinates::class);

        return new CoordinatesFieldsetInputFilter([
            'object_manager' => $this->getObjectManager(),
            'object_repository' => $this->getObjectManager()->getRepository(Coordinates::class),
            'translator' => $this->getTranslator(),
        ]);
    }
}
```

Nothing too special here. A function (`setupRequirements`) has been provided in the parent class to take away repeating
yourself a lot. What remains, when creating a basic FieldsetInputFilter, is just the creation of a new object and 
passing the requirements along.

## Create a FieldsetInputFilter with a FieldsetInputFilter

```php
class AddressFieldsetInputFilterFactory extends AbstractDoctrineFieldsetInputFilterFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        parent::setupRequirements($container, Address::class);

        /** @var CoordinatesFieldsetInputFilter $coordinatesFieldsetInputFilter */
        $coordinatesFieldsetInputFilter = $this->getInputFilterManager()->get(CoordinatesFieldsetInputFilter::class);
        $coordinatesFieldsetInputFilter->setRequired(false);

        return new AddressFieldsetInputFilter(
            $this->getObjectManager(),
            $this->getTranslator(),
            $coordinatesFieldsetInputFilter
        );
    }
}
```

Because the FieldsetInputFilter deviates from the standard, we have a modified Factory to go along with it. In the 
InputFilter class we have a modified `__construct` function; it requires an addition parameter (an InputFilter). 

As such, we should first get that InputFilter, using the `InputFilterManager` which has been set in the 
`setupRequirements` function. Here we also mark whether or not the additional Fieldset is required or not. 

## Create a FieldsetInputFilter with a Collection of FieldsetInputFilters

```php
class CityFieldsetInputFilterFactory extends AbstractDoctrineFieldsetInputFilterFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        parent::setupRequirements($container, City::class);

        /** @var AddressFieldsetInputFilter $addressFieldsetInputFilter */
        $addressFieldsetInputFilter = $this->getInputFilterManager()->get(AddressFieldsetInputFilter::class);
        $addressFieldsetInputFilter->setRequired(false);
        $addressFieldsetInputFilter->remove('city'); // Will be set with the City being created in base_fieldset

        /** @var CollectionInputFilter $addressFieldsetCollectionInputFilter */
        $addressFieldsetCollectionInputFilter = new CollectionInputFilter();
        $addressFieldsetCollectionInputFilter->setInputFilter($addressFieldsetInputFilter);
        $addressFieldsetCollectionInputFilter->setIsRequired(false);

        /** @var CoordinatesFieldsetInputFilter $coordinatesFieldsetInputFilter */
        $coordinatesFieldsetInputFilter = $this->getInputFilterManager()->get(CoordinatesFieldsetInputFilter::class);
        $coordinatesFieldsetInputFilter->setRequired(false);

        return new CityFieldsetInputFilter(
            $this->getObjectManager(),
            $this->getTranslator(),
            $addressFieldsetCollectionInputFilter,
            $coordinatesFieldsetInputFilter
        );
    }
}
```

Definitely the most tricky. This is InputFilterCeption hell. In these it's easy to make a small mistake and then spend
hours finding what you did where that was not what Zend Framework expected. Or you expected. Or... shit. Well, whatever!

Comes down to: create this slowly. You'll do this a hundred times and still make a small mistake! Trust me, I know... 

First up, get the Fieldset like you did for the earlier example. Modify it for the requirements of your current Form. In
this case we set it to be not required and we remove the `city` property (the no longer endless loop I told you about 
above). 

Next, create a new CollectionInputFilter. Make sure you **do not use the CollectionInputFilter provided by Zend 
Framework!!!** It contains a bug, and sadly my bug report has been open for a long time already. 

(The bug reported is that it will always try to validate and hydrate objects, also for the non-required child fieldsets, 
which may be empty, and thus should fail validation, but they don't, and then you get database errors, and that 
sucks - **deep breath**)

# Configuring it all

The examples above, and the [examples module](https://github.com/rkeet/zf-doctrine-form/examples) as a whole, run on 
configuration. The configuration for Forms, Fieldsets and InputFilters (with their Factories), is as follows:

```php
return [
    'form_elements' => [ // <== NOTICE THAT CONFIG NAME FOR FORMS AND FIELDSETS!!!
        'factories' => [
            AddressForm::class => AddressFormFactory::class,
            AddressFieldset::class => AddressFieldsetFactory::class,

            CityForm::class => CityFormFactory::class,
            CityFieldset::class => CityFieldsetFactory::class,

            CoordinatesForm::class => CoordinatesFormFactory::class,
            CoordinatesFieldset::class => CoordinatesFieldsetFactory::class,
        ],
    ],
    'input_filters' => [ // <== NOTICE THAT CONFIG NAME FOR JUST INPUT FILTERS!!!
        'factories' => [
            AddressFormInputFilter::class => AddressFormInputFilterFactory::class,
            AddressFieldsetInputFilter::class => AddressFieldsetInputFilterFactory::class,

            CityFormInputFilter::class => CityFormInputFilterFactory::class,
            CityFieldsetInputFilter::class => CityFieldsetInputFilterFactory::class,

            CoordinatesFormInputFilter::class => CoordinatesFormInputFilterFactory::class,
            CoordinatesFieldsetInputFilter::class => CoordinatesFieldsetInputFilterFactory::class,
        ],
    ],
];
```
 
# Ok that's it. 

If you read all of the above, you should be set to give it a shot. 

Good luck, have fun. 