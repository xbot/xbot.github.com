# GraphQL：RESTful之外的接口实现方案


和RESTfull一样，GraphQL也是一种基于HTTP的接口实现方式。它区别于前者的主要有两点：数据格式的自定义和请求的合并。

本质上，GraphQL是为了解决RESTful中前后端在业务逻辑上的耦合关系。在RESTful中，接口是为前端具体的业务需求定制的，从实现什么样的功能，到返回哪些数据，都是既定的，所以很难被其它业务逻辑共用，即使可以共用，前端也必须发送多个请求到后端，因而造成资源浪费、效率下降。

GraphQL实现的是一套逻辑积木，每个封装好的业务逻辑都是原子的，前端可以自由选择使用哪些，也可以定制返回数据的格式。并且这一切，都可以通过一次请求实现。

当然，GraphQL也有一些短板。比如缓存，对于RESTfull接口，可以根据操作的幂等性实现负载均衡层面的缓存，而对于GraphQL，由于请求数据格式灵活且可能很大，请求会用POST方式发送，这样就必须改变缓存的实现方式。再一点是嵌套的层级问题，GraphQL的灵活性允许查询类型之间彼此嵌套，如果层级过多，可能导致严重的性能和可用性问题，因此需要注意限制嵌套的层级。还有就是GraphQL的实现复杂度比RESTful要高，需要权衡使用哪种实现方案。

GraphQL用schema管理接口，可以根据业务等因素划分schema，例如需要权限验证的和公开的。每个schema包含两类接口：query和mutation，分别用来查询和变更数据。

下面在Laravel中简单实现一个query接口。

先安装[folklore/graphql](https://github.com/Folkloreatelier/laravel-graphql)库。

实现文章类型：

```php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class PostType extends GraphQLType {

    public function fields()
	{
		return [
			'id' => [
				'type' => Type::nonNull(Type::string()),
				'description' => 'The id of the post'
			],
			'title' => [
				'type' => Type::string(),
				'description' => 'Post title'
			]
		];
	}

    protected function resolveTitleField($root, $args)
	{
		return $root->post_title;
	}

}
```

实现文章的查询逻辑：

```php
namespace App\GraphQL\Query;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;
use App\Models\Post;

class PostQuery extends Query {

    public function type()
	{
		return Type::listOf(GraphQL::type('Post'));
    }

    public function resolve($root, $args)
	{
		if(isset($args['id'])) {
			return Post::where('id' , $args['id'])->get();
		} else {
			return Post::all();
		}
	}

}
```

在`config/graphql.php`中注册类型和逻辑：

```php
'schemas' => [
   'default' => [
       'query' => [
           'posts' => 'App\GraphQL\Query\PostQuery'
       ],
       'mutation' => [

       ]
   ],
],

'types' => [
   'Post' => 'App\GraphQL\Type\PostType',
],
```

请求及结果如下：

![](https://wx4.sinaimg.cn/large/006tNbRwly1fwvwx9cf6hj30gi0fhmyk.jpg)

GraphQL并不是RESTful的替代方案，是否用前者替代后者，取决于是否愿意为了灵活性而增加复杂度。两者有各自的适用领域，GraphQL更适合用来实现开放接口。


