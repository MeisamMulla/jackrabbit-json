# JackRabbit JSON Class Listings
This class retrieves Jackrabbit class listings and returns them in an easier to handle format for displaying the listings on a website.

## Usage
Initialize the class by passing the Organization ID as a parameter in the constructor:
```php
$jr = new MeisamMulla\Jackrabbit('123456');
```

Then use the `query()` method to get the resuts of your requests. The method accepts an array of parameters which can be found in the [JackRabbit documentation](http://jackrabbitcarehelp.com/guide2/default.aspx?pageid=filtering-grouping).

As an example:
```php
$jr = new MeisamMulla\Jackrabbit('123456');

$json = $jr->query([
    'Session' => '2018/19',
    'loc' => 'LOC1',
    'cat1' => 'Ballet',
]);
```

Will return an output similar to this one:

```json
[
    {  
        "name":"Duo Ballet",
        "description":"All Ballet Duo Students",
        "location":"Studio Location",
        "ages":"5 - 16",
        "day":"Friday",
        "link":"https:\/\/app.jackrabbitclass.com\/reg.asp?id=123456&amp;hc=&amp;initEmpty=&amp;hdrColor=&amp;WL=0&amp;preLoadClassID=000000&amp;loc=",
        "openings":0,
        "times":"9:00am-9:45am",
        "dates":"Jan 1st-Jun 1st"
   }
]
```

### Caching
The results will be cached in the `dance-cache` directory for 5 minutes by default. You can modify this behaviour by changing the `$cacheTime` property in the class. This value can be written in human readable english (ex. `-30 minutes` for a cache time of 30 minutes).