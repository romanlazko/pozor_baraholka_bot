<x-telegram::layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit announcement') }}
            </h2>
            <div class="flex-col">
                <x-telegram::a-buttons.secondary href="{{ route('pozor_baraholka_bot.announcement.index') }}" class="float-right">
                    {{ __("←Back") }}
                </x-telegram::a-buttons.secondary>
            </div>
        </div>
    </x-slot>
    <x-slot name="main">
        <div class="sm:flex grid sm:grid-cols-2 grid-cols-1 sm:space-x-2 justify-between sm:space-y-0 space-y-6">
            <div class="flex-col max-w-xl w-full sm:w-1/2 ">
                <x-telegram::white-block>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Edit announcement') }}
                    </h2>
            
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Use this form to edit announcement.') }}
                    </p>
                    <form method="post" action="{{ route('pozor_baraholka_bot.announcement.update', $announcement) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div>
                            <x-telegram::form.label :value="__('Images:')" />
                            <input id="images" type="file" name="images[]" multiple="multiple" accept="image/*" max="{{ 9-$announcement->photos->count() }}" class="mt-1 block w-full">
                            <div class="flex">
                                @foreach ($announcement->photos as $photo)
                                    <div class="flex-col sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/5 p-1 imagePreview">
                                        <img src="{{ $photo->url }}" class="h-40 object-cover object-center">
                                        <div class="flex items-center mt-2">
                                            <label for="image-{{ $photo->id }}" class="cursor-pointer text-blue-600 hover:underline mr-2">Delete</label>
                                            <input id="image-{{ $photo->id }}" type="checkbox" name="delete_images[]" value="{{ $photo->id }}" class="imageCheckbox">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('images')" />
                        </div>
                        
                        <div>
                            <x-telegram::form.label for="chat" :value="__('Chat:*')" />
                            <x-telegram::form.input id="chat" name="chat" type="text" class="mt-1 block w-full" :value="old('chat', $announcement->chat)" required autocomplete="chat" placeholder="Write chat. User invisible"/>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('chat')" />
                        </div>

                        <div>
                            <x-telegram::form.label for="city" :value="__('City:*')" />
                            <x-telegram::form.select id="city" name="city" class="mt-1 block w-full" required>
                                <option @selected($announcement->city == 'brno') value="brno">Brno</option>
                                <option @selected($announcement->city == 'prague') value="prague">Prague</option>
                            </x-telegram::form.select>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <div>
                            <x-telegram::form.label for="type" :value="__('Type:*')" />
                            <x-telegram::form.select id="type" name="type" class="mt-1 block w-full" required>
                                <option @selected($announcement->type == 'buy') value="buy">Buy</option>
                                <option @selected($announcement->type == 'sell') value="sell">Sell</option>
                            </x-telegram::form.select>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <div>
                            <x-telegram::form.label for="title" :value="__('Title:*')" />
                            <x-telegram::form.input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $announcement->title)" required autocomplete="title" placeholder="Write title. User visible"/>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-telegram::form.label for="caption" :value="__('Caption:*')" />
                            <x-telegram::form.input id="caption" name="caption" type="text" class="mt-1 block w-full" :value="old('caption', $announcement->caption)" required autocomplete="caption" placeholder="Write caption. User visible"/>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('caption')" />
                        </div>
                
                        <div>
                            <x-telegram::form.label for="cost" :value="__('Cost:*')" />
                            <x-telegram::form.input id="cost" name="cost" type="text" class="mt-1 block w-full" :value="old('cost', $announcement->cost)" required autocomplete="cost" placeholder="Write cost. User visible"/>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('cost')" />
                        </div>

                        <div>
                            <x-telegram::form.label for="category" :value="__('Category:*')" />
                            <x-telegram::form.select id="category" name="category" class="mt-1 block w-full" required>
                                <option @selected($announcement->category == 'clothes') value="clothes">#одежда</option>
                                <option @selected($announcement->category == 'accessories') value="accessories">#аксессуары</option>
                                <option @selected($announcement->category == 'for_home') value="for_home">#для_дома</option>
                                <option @selected($announcement->category == 'electronics') value="electronics">#электроника</option>
                                <option @selected($announcement->category == 'sport') value="sport">#спорт</option>
                                <option @selected($announcement->category == 'furniture') value="furniture">#мебель</option>
                                <option @selected($announcement->category == 'books') value="books">#книги</option>
                                <option @selected($announcement->category == 'games') value="games">#игры</option>
                                <option @selected($announcement->category == 'auto') value="auto">#авто_мото</option>
                                <option @selected($announcement->category == 'property') value="property">#недвижимость</option>
                                <option @selected($announcement->category == 'animals') value="animals">#животные</option>
                                <option @selected($announcement->category == 'other') value="other">#прочее</option>
                            </x-telegram::form.select>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('cost')" />
                        </div>

                        <div>
                            <x-telegram::form.label for="condition" :value="__('Condition:*')" />
                            <x-telegram::form.select id="condition" name="condition" class="mt-1 block w-full" required>
                                <option @selected($announcement->condition == 'new') value="new">New</option>
                                <option @selected($announcement->condition == 'used') value="used">Used</option>
                            </x-telegram::form.select>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <div>
                            <x-telegram::form.label for="status" :value="__('Status:')" />
                            <x-telegram::form.select id="status" name="status" class="mt-1 block w-full" required>
                                <option @selected($announcement->status == 'new') value="new">New</option>
                                <option @selected($announcement->status == 'published') value="published">Published</option>
                                <option @selected($announcement->status == 'rejected') value="rejected">Rejected</option>
                                <option @selected($announcement->status == 'canceled') value="canceled">Canceled</option>
                            </x-telegram::form.select>
                            <x-telegram::form.error class="mt-2" :messages="$errors->get('command')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-telegram::buttons.primary>{{ __('Save') }}</x-telegram::buttons.primary>
                        </div>
                    </form>
                </x-telegram::white-block>
            </div>
        </div>
    </x-slot>
    @section('script')
        <script>
            $('.imageCheckbox').change(function(){
                if ($(this).prop('checked')) {
                    $(this).closest('.imagePreview').find('img').addClass('opacity-40');
                }else{
                    $(this).closest('.imagePreview').find('img').removeClass('opacity-40');
                }
            });
        </script>
    @endsection
</x-telegram::layout>

