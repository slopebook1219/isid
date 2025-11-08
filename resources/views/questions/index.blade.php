<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            問題一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 select-none">

                    <div x-data="{ 
                        showEditModal: false, 
                        showCreateModal: false,
                        editingQuestion: { id: null, text: '', unit: '', updateUrl: '' },
                        newQuestion: { text: '', unit: '' }
                    }">


                        <table class="min-w-full divide-y divide-gray-200 table-fixed select-none">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="w-3/5 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        問題文
                                    </th>
                                    <th scope="col"
                                        class="w-1/5 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        単位
                                    </th>
                                    <th
                                        class="w-1/5 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($questions as $question)
                                <tr id="question-row-{{ $question->id }}">

                                    <td id="question-text-{{ $question->id }}"
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 truncate">
                                        {{ $question->text }}
                                    </td>

                                    <td id="question-unit-{{ $question->id }}"
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $question->unit }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button @click="
                                                        editingQuestion = {
                                                            id: {{ $question->id }},
                                                            text: '{{ addslashes($question->text) }}',
                                                            unit: '{{ addslashes($question->unit) }}',
                                                            updateUrl: '{{ route('questions.update', $question) }}'
                                                        };
                                                        showEditModal = true;
                                                    " class="text-indigo-600 hover:text-indigo-900">
                                                編集
                                            </button>
                                            <button @click="
                                                    if (confirm('「{{ $question->text }}」を本当に削除しますか？')) {
                                                        axios.delete(`{{ route('questions.destroy', $question->id)}}`)
                                                            .then(response => {
                                                            document.getElementById('question-row-{{ $question->id }}').remove();
                                                            message = response.data.message;
                                                        }) 
                                                        .catch(error => {
                                                            console.error('削除エラー:', error);
                                                            alert('削除に失敗しました。');
                                                        });
                                                    }
                                                " class="text-red-600 hover:text-red-900">
                                                削除
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3"
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        まだ質問が作成されていません。
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="flex justify-end mt-4 select-none">
                            <button @click="
                                newQuestion = { text: '', unit: '' };
                                showCreateModal = true;
                            "
                                class="inline-block bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                                ＋ 問題作成
                            </button>
                        </div>


                        <div x-show="showEditModal"
                            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full"
                            style="display: none;">

                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
                                @click.away="showEditModal = false">

                                <h3 class="text-lg font-medium text-gray-900 mb-4">編集</h3>

                                <div class="mt-4">
                                    <form @submit.prevent="
                                        axios.put(editingQuestion.updateUrl, {
                                            text: editingQuestion.text,
                                            unit: editingQuestion.unit
                                        })
                                        .then(response => {
                                            const updatedQuestion = response.data.question;
                                            
                                            const textElement = document.getElementById(`question-text-${updatedQuestion.id}`);
                                            if (textElement) {
                                                textElement.innerText = updatedQuestion.text;
                                            }

                                            const unitElement = document.getElementById(`question-unit-${updatedQuestion.id}`);
                                            if (unitElement) {
                                                unitElement.innerText = updatedQuestion.unit;
                                            }
                                            
                                            message = response.data.message;
                                            showEditModal = false;
                                        })
                                        .catch(error => {
                                            console.error('更新エラー:', error.response.data);
                                            alert('更新に失敗しました。');
                                        });
                                    ">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="editText"
                                                class="block font-medium text-sm text-gray-700">問題文</label>
                                            <input type="text" id="editText" x-model="editingQuestion.text" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </div>
                                        <div class="mb-4">
                                            <label for="editUnit"
                                                class="block font-medium text-sm text-gray-700">単位</label>
                                            <input type="text" id="editUnit" x-model="editingQuestion.unit" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </div>
                                        <div class="flex justify-end space-x-2 mt-4">
                                            <button type="button" @click="showEditModal = false"
                                                class="inline-block bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-gray-400 transition duration-150 ease-in-out">
                                                キャンセル
                                            </button>
                                            <button type="submit"
                                                class="inline-block bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                                                更新
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div x-show="showCreateModal"
                            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full"
                            style="display: none;">

                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
                                @click.away="showCreateModal = false">

                                <h3 class="text-lg font-medium text-gray-900 mb-4">作成</h3>

                                <div class="mt-4">
                                    <form @submit.prevent="
                                        axios.post('{{ route('questions.store') }}', {
                                            text: newQuestion.text,
                                            unit: newQuestion.unit
                                        })
                                        .then(response => {
                                            message = response.data.message;
                                            showCreateModal = false;
                                            location.reload();
                                        })
                                        .catch(error => {
                                            console.error('作成エラー:', error.response?.data || error);
                                            alert('作成に失敗しました。');
                                        });
                                    ">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="createText"
                                                class="block font-medium text-sm text-gray-700">問題文</label>
                                            <input type="text" id="createText" x-model="newQuestion.text" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </div>
                                        <div class="mb-4">
                                            <label for="createUnit"
                                                class="block font-medium text-sm text-gray-700">単位</label>
                                            <input type="text" id="createUnit" x-model="newQuestion.unit" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </div>
                                        <div class="flex justify-end space-x-2 mt-4">
                                            <button type="button" @click="showCreateModal = false"
                                                class="inline-block bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-gray-400 transition duration-150 ease-in-out">
                                                キャンセル
                                            </button>
                                            <button type="submit"
                                                class="inline-block bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                                                作成
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>