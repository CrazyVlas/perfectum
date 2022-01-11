<style>
.deleted{
    padding:7px;
}

#deleted{
    cursor: pointer;
}

#deleted:hover{
    color:black;
}

.product:hover{
opacity: 90%;
}
</style>


<!-- Добавление категории -->
<form method="post" class="row g-3" id="addCategory">
    <div class="col-lg-4 col-md-5 col-sm-6 col-12">
        <input type="text" class="form-control" id="categoryName" placeholder="Название" required>
    </div>

    <div class="col-lg-2 col-md-3 col-sm-6 col-12">
        <input type="submit" class="btn btn-primary mb-3" value="Добавить категорию">
    </div>
</form>


<!-- Добавление товара -->
<form method="post" class="row g-3" id="addProduct">
    <div class="col-lg-4">
        <input type="text" class="form-control" id="productName" placeholder="Название" required>
    </div>

    <div class="col-lg-2">
        <input type="number" class="form-control" min="0" id="price" placeholder="Цена" required>
    </div>

    <div class="col-lg-2">
        <select class="form-select" id="addProductCategory" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="col-lg-2">
        <select class="form-select" id="addProductStock" required>
            <option value="1">В наличии</option>
            <option value="0">Продано</option>
        </select>
    </div>

    <div class="col-lg-2">
        <input type="submit" class="btn btn-primary mb-3" value="Добавить продукт">
    </div>
</form>

<div class="row">

    <!-- Фильтр категорий -->
    <div class="col-lg-3">
        <select class="form-select" id="categories" aria-label="Default select example">
            <option value="0" selected>Все категории</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <!-- Фильтр товаров -->
    <div class="col-lg-3">
        <select class="form-select" id="stock" aria-label="Default select example">
            <option value="0" selected>Все товары</option>
            <option value="2">В наличии</option>
            <option value="1">Продано</option>
        </select>
    </div>

</div>

<div id="products" class="mt-3">

    <!-- Вывод товаров -->
    <?php foreach ($products as $product): ?>

        <?php $stock = ($product['stock'] ?  'bg-success' : 'bg-danger'); ?>
          <div class="row mt-2 p-3 text-light product <?= $stock;?>" id="product" data-id="<?= $product['id']; ?>">
            <div class="name col-lg-4">
                <?= $product['name']; ?>
            </div>

            <div class="name col-lg-2">
                <?= $product['category']; ?>
            </div>

            <div class="price col-lg-1">
                <?= $product['price']; ?>
            </div>

            <div class="created_at col-lg-2">
                <?= $product['created_at']; ?>
            </div>

            <div class="stock col-lg-2">
                <select class="form-select" aria-label="Default select example" id="productStock">
                        <option disabled selected>Наличие</option>
                        <option value="1">В наличии</option>
                        <option value="0">Продано</option>
                </select>
            </div>

            <div class="deleted col-lg-1 text-center">
                <i class="fas fa-trash-alt" id="deleted"></i>
            </div>
        </div>

    <?php endforeach ?>

</div>



<script>
const BASE_URL = "<?php echo base_url();?>";

    <!-- Фильтр категории -->
    $(document).on('change', '#categories',function() {
        const category = $(this).val();
        const stock = $("#stock").val();

        $.ajax({
            type: 'POST',
            url: BASE_URL+'product/productDetails',
            data: {
                categoryFilter: category,
                stockFilter: stock,
            },
            success: function (data){
                console.log(data);
                $('#products').html(data);
            }
        })
    });

    <!-- Фильтр в наличии/ продано -->
    $(document).on('change', '#stock',function() {
        const category = $("#categories").val();
        const stock = $(this).val();

        $.ajax({
            type: 'POST',
            url: BASE_URL+'product/productDetails',
            data: {
                categoryFilter: category,
                stockFilter: stock,
            },
            success: function (data){
                $('#products').html(data);
            }
        })
    });

    <!-- Изменить наличие продукта -->
    $(document).on('change', '#productStock', function (){
        const product = $(this).closest('#product');
        const stockProduct = $(this).val();
        const stockCategory = $('#stock').val();
        $.ajax({
            type: 'POST',
            url: BASE_URL+'product/productChangeStock',
            data: {
                stock: stockProduct,
                product: product.data('id'),
            },
            success: function () {
                console.log('Статус товара изменен');
                if (stockProduct === '1') {
                    $(product).removeClass('bg-danger').addClass('bg-success');
                }else if(stockProduct === '0'){
                    $(product).removeClass('bg-success').addClass('bg-danger');
                }

                if((+(stockCategory-1) !== +stockProduct) && (stockCategory !== '0')){

                    $(product).remove();
                }
            }
        })

    });

    <!-- Удалить продукт -->
    $(document).on('click', '#deleted', function () {
        const product = $(this).closest('#product');

        if (confirm('Удалить товар ?')) {
            $.ajax({
                type: 'POST',
                url: BASE_URL+'product/productDelete',
                data: {
                    product: product.data('id'),
                },
                success: function () {
                    alert('Товар удален');
                    $(product).remove();
                }
            })
        }
    });

    <!-- Добавление продукта -->
    $(document).on('submit', '#addProduct', function( event ) {
        const name = $('#productName').val();
        const price = $('#price').val();
        const stock = $('#addProductStock').val();
        const category = $('#addProductCategory').val();

        const categoryFilter = $("#categories").val();
        const stockFilter = $('#stock').val();

        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: BASE_URL+'product/add',
            data: {
                name: name,
                price: price,
                stock: stock,
                category: category,
                categoryFilter: categoryFilter,
                stockFilter: stockFilter,
            },
            success: function (data) {
            if (category === categoryFilter || categoryFilter == 0){          
                $('#products').html(data);
            }
                alert('Товар добавлен');
            }
        })

    });

    <!-- Добавление категории -->
    $(document).on('submit', '#addCategory', function( event ) {
        const name = $('#categoryName').val();
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: BASE_URL+'category/add',
            data: {
                name: name,
            },
            success: function (data) {
                $('#addProductCategory').append(`<option value="${data}">${name}</option>`);
                $('#categories').append(`<option value="${data}">${name}</option>`);
                alert('Категория добавлена');
            }
        })

    });

</script>