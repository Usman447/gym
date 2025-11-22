@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <div class="container-fluid">

            <!-- Error Log -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Heading -->
            <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
                @include('flash::message')
                <h1 class="page-title no-line-height">Python Integration Test
                    <small>Demonstration of PHP to Python communication</small>
                </h1>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Test Python Script Execution</div>
                        </div>
                        <div class="panel-body">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parameter">Enter Parameter to Pass to Python:</label>
                                        <input type="text" name="parameter" value="" class="form-control" id="parameter" placeholder="e.g., Hello World or 123">
                                        <small class="help-block">This value will be passed to the Python script</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="execute-python-btn">
                                        <i class="ion-play"></i> Execute Python Script
                                    </button>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Python Output:</h4>
                                    <div id="python-output" class="well" style="background-color: #f5f5f5; padding: 15px; min-height: 100px; font-family: monospace; white-space: pre-wrap;">
                                        <em>Click "Execute Python Script" to see the output here...</em>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <h5><strong>How it works:</strong></h5>
                                        <ol>
                                            <li>PHP receives the parameter from the form</li>
                                            <li>PHP uses Symfony Process component to execute Python script</li>
                                            <li>Python script receives the parameter via command line arguments</li>
                                            <li>Python processes the parameter and outputs the result</li>
                                            <li>PHP captures the output and displays it in the response</li>
                                        </ol>
                                        <p><strong>Python Script Location:</strong> <code>{{ base_path('python_scripts/test_script.py') }}</code></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@stop

@section('footer_scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#execute-python-btn').on('click', function() {
            var parameter = $('#parameter').val();
            
            if (!parameter || parameter.trim() === '') {
                alert('Please enter a parameter');
                return;
            }

            // Disable button and show loading
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="ion-load-a"></i> Executing...');
            $('#python-output').html('<em>Executing Python script...</em>');

            // Make AJAX request
            $.ajax({
                url: '{{ action("PythonTestController@execute") }}',
                type: 'POST',
                data: {
                    parameter: parameter,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        var output = '=== Execution Successful ===\n';
                        output += 'Method: ' + response.method + '\n';
                        output += 'Parameter Sent: ' + response.parameter + '\n\n';
                        output += '=== Python Output ===\n';
                        output += response.output;
                        $('#python-output').html(output);
                    } else {
                        $('#python-output').html('ERROR: ' + (response.error || 'Unknown error occurred'));
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'ERROR: Failed to execute Python script';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg += '\n' + xhr.responseJSON.error;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.output) {
                        errorMsg += '\n\nPython Error Output:\n' + xhr.responseJSON.output;
                    }
                    $('#python-output').html(errorMsg);
                },
                complete: function() {
                    // Re-enable button
                    btn.prop('disabled', false).html('<i class="ion-play"></i> Execute Python Script');
                }
            });
        });

        // Allow Enter key to trigger execution
        $('#parameter').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#execute-python-btn').click();
            }
        });
    });
</script>
@stop

