<ul class="menu-tree">
    @foreach ($menus as $parent)
    <li class="parent">

        <label>
            <input type="checkbox"
                class="parent-checkbox"
                value="{{ $parent->id }}"
                name="menu_id[]"
                {{ in_array($parent->id, $roleMenus) ? 'checked' : '' }}>
            <strong class="toggle">▶ {{ $parent->name }}</strong>
        </label>

        @if ($parent->children->count())
        <ul>
            @foreach ($parent->children as $child)
            <li class="child">
                <label>
                    <input type="checkbox"
                        class="child-checkbox"
                        value="{{ $child->id }}"
                        name="menu_id[]"
                        data-parent="{{ $parent->id }}"
                        {{ in_array($child->id, $roleMenus) ? 'checked' : '' }}>
                    {{ $child->name }}
                </label>
            </li>
            @endforeach
        </ul>
        @endif

    </li>
    @endforeach
</ul>