<script>
    Config = {
        'routes': {
            'upload_image': "{{ route('image.upload', ['onedrive' => app('onedrive')->id]) }}",
            'upload_file': "{{ route('admin.onedrive.file.upload', ['onedrive' => app('onedrive')->id]) }}",
            'copy': "{{ route('admin.onedrive.copy', ['onedrive' => app('onedrive')->id]) }}",
            'move': "{{ route('admin.onedrive.move', ['onedrive' => app('onedrive')->id]) }}",
            'path2id': "{{ route('admin.onedrive.path2id', ['onedrive' => app('onedrive')->id]) }}",
            'share': "{{ route('admin.onedrive.share', ['onedrive' => app('onedrive')->id]) }}",
            'delete_share': "{{ route('admin.onedrive.share.delete', ['onedrive' => app('onedrive')->id]) }}",
            'upload_url': "{{ route('admin.onedrive.url.upload', ['onedrive' => app('onedrive')->id]) }}",
        },
        '_token': '{{ csrf_token() }}'
    };
</script>