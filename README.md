# SilverStripe NestedCheckboxSetField

This adds an extra level to a CheckboxSetField, which provides some structure to a large dataset.

## Usage

The code below defines the structure of the nested checkbox set and creates the field to add to the edit form in the CMS.

First, define the DataObjects that structure the nested checkbox set. The parent object `TagCategory` will represent the
top level of the nested structure.

```php
class TagCategory extends DataObject {

    private static $db = array(
        'Title' => 'Varchar(100)'
    );

    private static $has_many = array(
        'Tags' => 'Tag'
    );

}
```

Next, define the child DataObject - `Tag` will represent the second level of the nested checkbox set structure.

```php
class Tag extends DataObject {

    private static $db = array(
        'Title' => 'Varchar(100)'
    );

    private static $has_one = array(
        'Category' => 'TagCategory'
    );

}
```

Create the `NestedCheckboxSetField` and add it to the CMS edit form.

```php
class Page extends SiteTree {

    private static $many_many = array(
        'PageTags' => 'Tag'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $source = NestedCheckboxSetFieldSourceGenerator::generate(
            TagCategory::get(),
            function (TagCategory $tagCategory) {
                return $tagCategory->Tags();
            }
        );

        $field = new NestedCheckboxSetField(
            'PageTags',
            'Page tags',
            $source
        );

        $fields->insertBefore($field, 'Content');
    }

}
```

The source array for the field is generated using the `NestedCheckboxSetFieldSourceGenerator::generate` method. This
method returns a nested array structure that will look something like this (data dependent):

```php
array(
    'Tag Category One' => array(
        '1' => 'Tag One',
        '2' => 'Tag Two'
    ),
    'Test Top Two' => array(
        '3' => 'Tag Three'
    )
)
```

The `generate` method takes two arguments, the first being a `DataList` containing the top level items for the nested
checkbox set. The second argument is a callback that receives a single item from the first `DataList` and can make use
of that item to return child items in another `DataList`.

So in the example above we can see that the parent items are going to be `TagCategory`'s and each child list will be
made up of each `TagCategory`'s related `Tag`'s.

Once the source array is generated the rest is standard field creation - `NestedCheckboxSetField` extends the core
`CheckboxSetField` and can be used in the same way.

The source generator assumes that the `DataObject`s that it works with have `ID` and `Title` fields available. If your
`DataObject` does not have these fields consider implementing `getID` and `getTitle` methods on your `DataObject` so
that relevant data is contained in the generated source array.
