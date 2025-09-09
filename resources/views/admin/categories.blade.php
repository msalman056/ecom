@extends('layouts.admin')
@section('content')
 <div class="main-content-inner">
                            <div class="main-content-wrap">
                                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                                    <h3>categorys</h3>
                                    <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                                        <li>
                                            <a href="{{ route('admin.index') }}">
                                                <div class="text-tiny">Dashboard</div>
                                            </a>
                                        </li>
                                        <li>
                                            <i class="icon-chevron-right"></i>
                                        </li>
                                        <li>
                                            <div class="text-tiny">categories</div>
                                        </li>
                                    </ul>
                                </div>

                                <div class="wg-box">
                                    <div class="flex items-center justify-between gap10 flex-wrap">
                                        <div class="wg-filter flex-grow">
                                            <form class="form-search">
                                                <fieldset class="name">
                                                    <input type="text" placeholder="Search here..." class="" name="name"
                                                        tabindex="2" value="" aria-required="true" required="">
                                                </fieldset>
                                                <div class="button-submit">
                                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                        <a class="tf-button style-1 w208" href="{{ route('admin.categories.add') }}"><i
                                                class="icon-plus"></i>Add new</a>
                                    </div>
                                    <div class="wg-table table-all-user">
                                        <div class="table-responsive">
                                            @if (session('success'))
                                            <div class="alert alert-success">{{ session('success') }}</div>
                                            @endif
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Slug</th>
                                                        <th>Products</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($categories as $category)
                                                    <tr>
                                                        <td>{{ $category->id }}</td>
                                                        <td class="pname">
                                                            <div class="image">
                                                                <img src="{{ asset('uploads/categories') }}/{{ $category->image }}" alt="{{ $category->name }}" class="image">
                                                            </div>
                                                            <div class="name">
                                                                <a href="#" class="body-title-2">{{ $category->name }}</a>
                                                            </div>
                                                        </td>
                                                        <td>{{ $category->slug }}</td>
                                                        <td><a href="#" target="_blank">{{ $category->products_count }}</a></td>
                                                        <td>
                                                            <div class="list-icon-function">
                                                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="item edit" title="Edit category">
                                                                    <i class="icon-edit-3"></i>
                                                                </a>

                                                                <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="item text-danger delete" onclick="return confirm('Are you sure you want to delete this category?')">
                                                                    <i class="icon-trash-2"></i>
                                                                </button>
                                                            </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                                           @if(method_exists($categories, 'links'))
                                               {{ $categories->links('pagination::bootstrap-5') }}
                                           @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
@endsection

@push('scripts')
<script>
    $(function(){
        $("#myFile").on("change",function(e){
            const photoInp = $("#myFile");
            const [file] = this.files;
            if(file)
            {
                $("#imgpreview img").attr('src',URL.createObjectURL(file));
                $("#imgpreview").show();
            }
        });

        $("input[name='name']").on("change",function(){
            $("input[name='slug']").val(StringToSlug($(this).val()));
        });

    });

    function StringToSlug(Text)
    {
        return Text.toLowerCase()
        .replace(/[^\w ]+/g,"")
        .replace(/ +/g,"-");
    }
</script>
@endpush
