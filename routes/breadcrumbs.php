<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Webkul\Category\Contracts\Category;
use Webkul\Product\Contracts\Product;


// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push(('Home'), route('shop.home.index'));
});

Breadcrumbs::for('category', function (BreadcrumbTrail $trail, Category $category) {
    $trail->parent('home');

    $parentCategory = $category->parent;
    while ($parentCategory && $parentCategory->id !== 1) {
        $trail->push($parentCategory->name, route('shop.productOrCategory.index', $parentCategory->url_path));
        $parentCategory = $parentCategory->parent;
    }
    $trail->push($category->name, route('shop.productOrCategory.index', $category->url_path));
});

Breadcrumbs::for('product', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('home');

    $productCategory = $product->product->categories->first();

    $parentCategory = $productCategory->parent;
    while ($parentCategory && $parentCategory->id !== 1) {
        $trail->push($parentCategory->name, route('shop.productOrCategory.index', $parentCategory->url_path));
        $parentCategory = $parentCategory->parent;
    }
    $trail->push($productCategory->name, route('shop.productOrCategory.index', $productCategory->url_path));

    $trail->push($product->name, route('shop.productOrCategory.index', $product->url_key));
});

