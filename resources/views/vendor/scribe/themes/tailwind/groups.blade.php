@foreach($groupedEndpoints as $group)
    @include("scribe::themes.tailwind.group", ['group' => $group])
@endforeach
