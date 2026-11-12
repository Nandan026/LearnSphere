<div class="row g-3">
<div class="col-md-8"><label class="form-label">Title</label><input name="title" class="form-control" value="{{ old('title', $course->title ?? '') }}" required></div>
<div class="col-md-4"><label class="form-label">Category</label>
<select name="category_id" class="form-select" required>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(($course->category_id ?? null)==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
<div class="col-12"><label class="form-label">Short description</label><input name="short_description" class="form-control" value="{{ old('short_description', $course->short_description ?? '') }}"></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4">{{ old('description', $course->description ?? '') }}</textarea></div>
<div class="col-md-3"><label class="form-label">Level</label><select name="level" class="form-select">@foreach(['beginner','intermediate','advanced'] as $lv)<option value="{{ $lv }}" @selected(($course->level ?? 'beginner')===$lv)>{{ $lv }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Language</label><input name="language" class="form-control" value="{{ old('language', $course->language ?? 'en') }}"></div>
<div class="col-md-3"><label class="form-label">Price</label><input name="price" type="number" step="0.01" class="form-control" value="{{ old('price', $course->price ?? 0) }}"></div>
<div class="col-md-3"><label class="form-label">Currency</label><input name="currency" class="form-control" value="{{ old('currency', $course->currency ?? 'INR') }}"></div>
<div class="col-md-3"><label class="form-label">Est. minutes</label><input name="estimated_minutes" type="number" class="form-control" value="{{ old('estimated_minutes', $course->estimated_minutes ?? 60) }}"></div>
</div>
