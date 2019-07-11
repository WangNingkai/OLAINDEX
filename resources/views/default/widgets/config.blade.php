<script>
    Config = {
        'routes': {
            'upload_image': "{{ route('image.upload', ['onedrive' => app('onedrive')->id]) }}"
        },
        '_token': '{{ csrf_token() }}',
    };
</script>