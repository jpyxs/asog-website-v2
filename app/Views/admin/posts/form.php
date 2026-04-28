<?php
/**
 * Post form — shared for Create and Edit.
 *
 * Variables:
 *   $pageTitle  — "New Post" or "Edit Post"
 *   $post       — (edit only) associative array of current values
 *   $activePage — always "posts"
 */

$isEdit  = isset($post);
$formUrl = $isEdit
    ? site_url('admin/posts/' . $post['id'])
    : site_url('admin/posts');
?>

<link rel="stylesheet" href="<?= base_url('assets/css/adminPostForm.css') ?>">

<form action="<?= $formUrl ?>" method="POST" enctype="multipart/form-data" id="postForm">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
    <input type="hidden" name="_method" value="PUT" />
    <?php endif; ?>
    <div class="form-card">
        <div class="form-grid">

            <!-- Title -->
            <div class="field">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?= esc($isEdit ? $post['title'] : old('title')) ?>"
                    required placeholder="Post title">
            </div>

            <!-- Category + Author -->
            <div class="form-row">
                <div class="field">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <?php
                        $cat = $isEdit ? $post['category'] : old('category');
                        foreach (['news', 'events', 'features'] as $c):
                        ?>
                        <option value="<?= $c ?>" <?= $cat === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="authorName">Author</label>
                    <input type="text" id="authorName" name="authorName"
                        value="<?= esc($isEdit ? $post['authorName'] : (old('authorName') ?: 'ASOG TBI')) ?>"
                        placeholder="Author name">
                </div>
            </div>

            <!-- Published date -->
            <div class="field">
                <label for="publishedAt">Publication date</label>
                <input type="date" id="publishedAt" name="publishedAt"
                    value="<?= esc($isEdit && !empty($post['publishedAt']) ? date('Y-m-d', strtotime($post['publishedAt'])) : old('publishedAt')) ?>"
                    placeholder="Leave blank for today">
            </div>

            <!-- Short description -->
            <div class="field">
                <label for="shortDescription">Short description</label>
                <textarea id="shortDescription" name="shortDescription" rows="2"
                    placeholder="A brief summary shown in previews"><?= esc($isEdit ? $post['shortDescription'] : old('shortDescription')) ?></textarea>
            </div>

            <!-- Content (Quill) -->
            <div class="field">
                <label>Content</label>
                <div class="editor-wrap">
                    <div class="quill-editor"><?= $isEdit ? $post['content'] : old('content') ?></div>
                    <input type="hidden" name="content" class="quill-content"
                        value="<?= esc($isEdit ? $post['content'] : old('content')) ?>">
                </div>
            </div>

            <!-- Image upload -->
            <div class="field">
                <label>Cover image</label>
                <div class="upload-zone" id="uploadZone">
                    <input type="file" name="image" id="imageInput" accept="image/*">
                    <div class="label" id="uploadLabel"><strong>Click to upload</strong> or drag an image here</div>
                    <div class="upload-preview" id="uploadPreview">
                        <?php if ($isEdit && ! empty($post['imagePath'])): ?>
                        <img src="<?= site_url($post['imagePath']) ?>" alt="">
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($isEdit && ! empty($post['imagePath'])): ?>
                <p style="font-size:.62rem;color:#94a3b8;margin-top:.35rem">Click the image to replace the current cover
                </p>
                <?php endif; ?>
            </div>

            <!-- Toggles -->
            <div class="switch-row">
                <label class="switch">
                    <input type="checkbox" name="isPublished" value="1"
                        <?= ($isEdit && $post['isPublished']) ? 'checked' : '' ?>>
                    <span class="track"></span>
                    Publish
                </label>
                <label class="switch">
                    <input type="checkbox" name="isFeatured" value="1"
                        <?= ($isEdit && ! empty($post['isFeatured'])) ? 'checked' : '' ?>>
                    <span class="track"></span>
                    Featured <span style="font-size:.65rem;color:#94a3b8;font-weight:400">(up to 5 show in hero)</span>
                </label>
            </div>

            <div class="form-actions">
                <a href="<?= site_url('admin/posts') ?>" class="btn-o">← Back to posts</a>
                <span style="flex:1"></span>
                <button type="submit" class="btn-p">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <?= $isEdit ? 'Save changes' : 'Publish post' ?>
                </button>
            </div>
        </div>
    </div>
</form>

<script src="<?= base_url('assets/js/admin/posts/form.js') ?>"></script>