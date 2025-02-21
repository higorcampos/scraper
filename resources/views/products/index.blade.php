<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .pagination svg {
            width: 20px;
            height: 20px;
        }

        .pagination .page-link {
            background-color: transparent !important;
            border: 1px solid #ddd;
            /* Opcional: para manter o contorno */
            color: #007bff;
            /* Mantém a cor do texto */
        }

        .flex.justify-between.flex-1.sm\:hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row" style="padding-top: 70px;">
            <div class="col-md-3">
                <h4>Categories</h4>
                <ul class="list-group">
                    @foreach ($categories as $category)
                        <li class="list-group-item">
                            <a href="{{ route('products.index', ['category' => $category->id]) }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-9">
                <h4>Books</h4>
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="Search books..."
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 text-center">
                                <figure class="mt-3">
                                    <img src="https://books.toscrape.com/{{ $product->image }}"
                                        alt="{{ $product->title }}" class="product-img">
                                </figure>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $product->title }}</h5>
                                    <p class="card-text text-truncate">
                                        {{ \Illuminate\Support\Str::words($product->description, 40, '...') }}</p>
                                    <p class="font-weight-bold text-primary">Price:
                                        ${{ number_format($product->price, 2) }}
                                    </p>
                                    <p class="card-text"><small class="text-muted">Category:
                                            {{ $product->category->name }}</small></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            {{ $products->links() }}
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
