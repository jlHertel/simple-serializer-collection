<?php

class Author
{
    private $name;
    private $posts;

    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getPosts()
    {
        return $this->posts;
    }

    public function setPosts(array $posts)
    {

        $this->posts->clear();

        foreach ($posts as $post) {
            $this->addPosts($post);
        }
    }

    public function addPosts(\Post $post)
    {
        $this->posts->add($post);
    }

    public function removePosts(Post $post)
    {
        $this->posts->removeElement($post);
    }
}

class Post
{
    private $title;

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
}

require 'vendor/autoload.php';

$app = new \Silex\Application();

$app->register(new Silex\Provider\SerializerServiceProvider());

$app['serializer.normalizers'] = function () use ($app) {
    $propertyInfo = new \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor();

    return [
        new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer(),
        new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer(null, null, null, $propertyInfo),
    ];
};

$app->get('/', function (\Silex\Application $app) {

    $sampleJson = '{"name": "author name", "posts":[{"title": "some post 1"}, {"title": "some post 2"}]}';

    $author = $app['serializer']->deserialize($sampleJson, \Author::class, 'json');

    var_dump($author);

    return new \Symfony\Component\HttpFoundation\Response("It works!");
});

$app->run();
