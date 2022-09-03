@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{route('product.index')}}" method="get" class="card-header">
            {{-- @csrf --}}
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" value="{{request()->get('title')}}" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="">--Select A Variant--</option>
                        @foreach($variants as $variant)
                            <optgroup label="{{ $variant->title }}">
                                @foreach($variant->product_variant as $pVariant)
                                    <option {{ ($pVariant->variant == request()->get('variant'))?'selected':'' }} value="{{ $pVariant->variant }}">{{ $pVariant->variant }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" value="{{request()->get('price_from')}}" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" value="{{request()->get('price_to')}}" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" value="{{request()->get('date')}}" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th width="10">Description</th>
                            <th>Variant</th>
                            <th width="150px">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($productData as $key => $product)
                            
                            <tr>
                                <td>{{ ($productData->currentpage()-1) * $productData->perpage() + $key + 1 }}</td>
                                <td>{{$product->title}} <br> Created at : {{Carbon\Carbon::parse($product->created_at)->format('d-M-Y')}}</td>
                                <td>{{$product->description}}</td>
                                <td>
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                        @foreach ($product->variant_price as $productPrice)
                                            <dt class="col-sm-3 pb-0">
                                                {{$productPrice->variant_two->variant ?? null}}/
                                                {{$productPrice->variant_one->variant ?? null}}/
                                                {{$productPrice->variant_three->variant ?? null}} <br/>
                                            </dt>
                                            <dd class="col-sm-9">
                                                <dl class="row mb-0">
                                                    {{-- {{$productPrice->variant_price}} --}}
                                                    <dt class="col-sm-4 pb-0">Price : {{ number_format($productPrice->price,2) }}</dt>
                                                    <dd class="col-sm-8 pb-0">InStock : {{ number_format($productPrice->stock,2) }}</dd>
                                                </dl>
                                            </dd>
                                        @endforeach
                                    </dl>
                                    <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            No Product Found
                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{($productData->currentpage()-1)*$productData->perpage()+1}} to {{$productData->currentpage()*$productData->perpage()}} out of {{$productData->total()}}</p>
                </div>
                <div class="col-md-6">
                    {{$productData->links()}}
                </div>
            </div>
        </div>
    </div>

@endsection
