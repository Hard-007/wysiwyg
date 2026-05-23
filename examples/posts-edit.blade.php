{{--
|--------------------------------------------------------------------------
| EXAMPLE: resources/views/posts/edit.blade.php
|--------------------------------------------------------------------------
| A second example showing how to use the FlexWave WYSIWYG editor
| when editing an existing model.
--}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>

    @wysiwygStyles
</head>
<body>

<div class="container" style="max-width:860px; margin:40px auto; padding:0 20px; font-family:system-ui,sans-serif">

    <h1 style="margin-bottom:8px">Edit Post</h1>
    <p style="margin-top:0; margin-bottom:24px; color:#666">
        This example uses the editor to load and update an existing HTML body.
    </p>

    @if ($errors->any())
        <div style="background:#fef2f2; border:1px solid #fca5a5; border-radius:6px; padding:12px 16px; margin-bottom:20px">
            <ul style="margin:0; padding-left:20px; color:#b91c1c">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('posts.update', $post) }}">
        @csrf
        @method('PUT')

        <div style="margin-bottom:20px">
            <label for="title" style="display:block; font-weight:600; margin-bottom:6px">
                Title <span style="color:#ef4444">*</span>
            </label>
            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title', $post->title) }}"
                placeholder="Post title"
                style="width:100%; height:40px; padding:0 12px; border:1.5px solid #e2e2ec; border-radius:6px; font-size:15px; box-sizing:border-box"
            >
        </div>

        <div style="margin-bottom:20px">
            <label style="display:block; font-weight:600; margin-bottom:8px">
                Content <span style="color:#ef4444">*</span>
            </label>

            <x-flexwave-editor
                name="content"
                :value="old('content', $post->content)"
                placeholder="Update the article body..."
                :height="480"
                dark-mode="auto"
                :required="true"
            />
        </div>

        <div style="margin-bottom:20px">
            <label for="category" style="display:block; font-weight:600; margin-bottom:6px">Category</label>
            <select
                id="category"
                name="category"
                style="height:40px; padding:0 10px; border:1.5px solid #e2e2ec; border-radius:6px; min-width:200px"
            >
                <option value="">Select a category</option>
                <option value="news" {{ old('category', $post->category) === 'news' ? 'selected' : '' }}>News</option>
                <option value="tutorial" {{ old('category', $post->category) === 'tutorial' ? 'selected' : '' }}>Tutorial</option>
                <option value="opinion" {{ old('category', $post->category) === 'opinion' ? 'selected' : '' }}>Opinion</option>
            </select>
        </div>

        <div style="display:flex; gap:10px">
            <button
                type="submit"
                style="height:40px; padding:0 24px; background:#111827; color:#fff; border:none; border-radius:6px; font-weight:600; cursor:pointer; font-size:14px"
            >
                Save Changes
            </button>
            <a
                href="{{ route('posts.index') }}"
                style="height:40px; padding:0 20px; display:inline-flex; align-items:center; border:1.5px solid #e2e2ec; border-radius:6px; color:#555; text-decoration:none; font-size:14px"
            >
                Cancel
            </a>
        </div>
    </form>

    <p style="margin-top:16px; font-size:13px; color:#888">
        Tip: toolbar actions keep the current selection even after you click a button.
    </p>
</div>

@wysiwygScripts

</body>
</html>
