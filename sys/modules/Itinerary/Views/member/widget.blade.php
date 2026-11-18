<script>
    @if($reservation_passed)
        window.itnWidget = {
            getOption: function() {
                return {
                    services: ['chatting', 'voice', 'video', 'coBrowsing']
                }
            }
        };
    @else
        window.itnWidget = {
            getOption: function() {
                return {
                    services: ['chatting']
                }
            }
        };
    @endif
</script>
@if (isset($widgets))
    @foreach($widgets as $widget)
        @if ($widget->status == 1)
            <script type="text/javascript" src="{{asset($widget->source_file_path)}}"></script>
        @endif
    @endforeach
    @if(!$reservation_passed)
        <script type="text/javascript" src="{{asset("module/itinerary/js/checkReservationPassed.js")}}"></script>
    @endif
@endif


