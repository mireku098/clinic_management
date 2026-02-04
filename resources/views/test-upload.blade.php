<!DOCTYPE html>
<html>
<head>
    <title>File Upload Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>File Upload Test</h1>
        
        <form method="POST" action="{{ route('test.upload.post') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="test_file" class="form-label">Select File</label>
                <input type="file" class="form-control" id="test_file" name="test_file" accept="image/jpeg,image/jpg,image/png" required>
            </div>
            <div class="mb-3">
                <label for="test_name" class="form-label">Test Name</label>
                <input type="text" class="form-control" id="test_name" name="test_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload File</button>
        </form>
        
        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
        @endif
        
        @if($uploaded_file)
            <div class="mt-4">
                <h3>Upload Result</h3>
                <p><strong>File Path:</strong> {{ $uploaded_file['path'] }}</p>
                <p><strong>File Size:</strong> {{ $uploaded_file['size'] }} bytes</p>
                <p><strong>File Type:</strong> {{ $uploaded_file['mime'] }}</p>
                <p><strong>Original Name:</strong> {{ $uploaded_file['original'] }}</p>
                
                @if($uploaded_file['image_url'])
                    <h4>Preview:</h4>
                    <img src="{{ $uploaded_file['image_url'] }}" style="max-width: 300px;" class="img-fluid">
                @endif
            </div>
        @endif
        
        @if($all_files)
            <div class="mt-4">
                <h3>All Uploaded Files</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Path</th>
                            <th>Size</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($all_files as $file)
                            <tr>
                                <td>{{ $file->name }}</td>
                                <td>{{ $file->path }}</td>
                                <td>{{ $file->size }}</td>
                                <td>{{ $file->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
