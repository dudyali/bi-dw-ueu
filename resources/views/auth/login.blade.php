@extends('layouts.master')

@section('title')
    <title>Login - DW UTS DUDY ALI</title>
@endsection

@section('content')
    <h1 class="text-center">DW UTS DUDY ALI</h1>
    <div class="text-center">Universitas Esa Unggul</div>
    <div class="mt-4">
        <div class="col-md-6 mx-auto">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Oops, terjadi kesalahan!</strong> <br>
                    @foreach ($errors->all() as $error)
                        -- {{ $error }} <br>
                    @endforeach
                </div>
            @endif
            

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('login') }}" class="form-horizontal mt-3" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <input type="submit" class="btn btn-primary mt-5" value="Login">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
  
@endsection