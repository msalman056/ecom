@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Edit Slide</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('admin.slider') }}">
                        <div class="text-tiny">Slider</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Edit Slide</div>
                </li>
            </ul>
        </div>

        <!-- edit slider -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" 
                  action="{{ route('admin.slider.update', $slide->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Title -->
                <fieldset class="name">
                    <div class="body-title">Title <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Title" 
                           name="title" value="{{ old('title', $slide->title) }}" required>
                </fieldset>

                <!-- Tagline -->
                <fieldset class="name">
                    <div class="body-title">Tagline</div>
                    <input class="flex-grow" type="text" placeholder="Tagline" 
                           name="tagline" value="{{ old('tagline', $slide->tagline) }}">
                </fieldset>

                <!-- Subtitle -->
                <fieldset class="name">
                    <div class="body-title">Subtitle</div>
                    <input class="flex-grow" type="text" placeholder="Subtitle" 
                           name="subtitle" value="{{ old('subtitle', $slide->subtitle) }}">
                </fieldset>

                <!-- Link -->
                <fieldset class="name">
                    <div class="body-title">Link</div>
                    <input class="flex-grow" type="url" placeholder="https://example.com" 
                           name="link" value="{{ old('link', $slide->link) }}">
                </fieldset>

                <!-- Image Upload -->
                <fieldset>
                    <div class="body-title">Upload Image</div>
                    <div class="upload-image flex-grow">
                        <div class="item up-load">
                            <label class="uploadfile" for="imageInput">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                    <img src="{{ $slide->image ? asset('uploads/sliders/'.$slide->image) : '' }}" 
                                         id="previewImage" class="effect8" 
                                         alt="Preview" 
                                         style="{{ $slide->image ? 'display:block;' : 'display:none;' }} max-width:150px; margin-top:10px;">
                                </span>
                                <span class="body-text">
                                    Drop your image here or 
                                    <span class="tf-color">click to browse</span>
                                </span>
                                <input type="file" id="imageInput" name="image" accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>

                <!-- Submit -->
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Live preview for uploaded image
    document.getElementById("imageInput").addEventListener("change", function(event) {
        let reader = new FileReader();
        reader.onload = function(){
            let preview = document.getElementById("previewImage");
            preview.src = reader.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>
@endpush
