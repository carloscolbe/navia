# Helper methods

Navia has several helper functions that are ready to use. Here you can find the list of available function that may speed up your development.

## Thumbnails URL

Navia will generate thumbnails for Image field type when you specify the [additional field options](../bread/introduction.md#additional-field-options).

After you have your thumbnails generated, you may want to display the thumbnails in your view or get the thumbnail URL. In order to do that you need to add `Resizable` traits to your model.

```php
use Navia\Traits\Resizable;

class Post extends Model
{
    use Resizable;
}
```

### Display a single image

```php
@foreach($posts as $post)
    <img src="{{Navia::image($post->thumbnail('small'))}}" />
@endforeach
```

Or you can specify the optional image field name \(attribute\), default to `image`

```php
@foreach($posts as $post)
    <img src="{{Navia::image($post->thumbnail('small', 'photo'))}}" />
@endforeach
```

### Display multiple images

```php
@foreach($posts as $post)
    $images = json_decode($post->images);
    @foreach($images as $image)
        <img src="{{ Navia::image($post->getThumbnail($image, 'small')) }}" />
    @endforeach
@endforeach
```

