@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Slide</h3>
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
                    <div class="text-tiny">New Slide</div>
                </li>
            </ul>
        </div>

        <!-- new slider -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" 
                  action="{{ route('admin.slider.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf

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
                           name="title" value="{{ old('title') }}" required>
                </fieldset>
                @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <!-- Tagline -->
                <fieldset class="name">
                    <div class="body-title">Tagline</div>
                    <input class="flex-grow" type="text" placeholder="Tagline" 
                           name="tagline" value="{{ old('tagline') }}">
                </fieldset>
                @error('tagline')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <!-- Subtitle -->
                <fieldset class="name">
                    <div class="body-title">Subtitle</div>
                    <input class="flex-grow" type="text" placeholder="Subtitle" 
                           name="subtitle" value="{{ old('subtitle') }}">
                </fieldset>
                @error('subtitle')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <!-- Link -->
                <fieldset class="name">
                    <div class="body-title ">Link</div>
                    <input class="flex-grow" type="url" placeholder="https://example.com" 
                           name="link" value="{{ old('link') }}">
                </fieldset>
                @error('link')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <!-- Image Upload -->
                <fieldset>
                    <div class="body-title">Upload Image <span class="tf-color-1">*</span></div>
                    <div class="upload-image flex-grow">
                        <div class="item up-load">
                            <label class="uploadfile" for="imageInput">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                    <img src="{{ old('image') }}" id="previewImage" class="effect8" 
                                         alt="Preview" style="display:none; max-width:150px; margin-top:10px;">
                                </span>
                                <span class="body-text">
                                    Drop your image here or 
                                    <span class="tf-color">click to browse</span>
                                </span>
                                <input type="file" id="imageInput" name="image" accept="image/*" required>
                            </label>
                        </div>
                    </div>
                </fieldset>
                @error('image')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <!-- Submit -->
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Fix scroll issue */
    html, body {
        height: auto !important;
        overflow-y: auto !important;
    }
    .main-content-inner,
    .main-content-wrap,
    .wg-box {
        min-height: auto !important;
        height: auto !important;
        overflow: visible !important;
    }
</style>
@endpush

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
