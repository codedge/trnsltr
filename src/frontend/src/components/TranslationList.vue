<template>
    <v-layout row wrap>
        <v-flex>
            <v-data-table :headers="headers"
                          :items="translations"
                          :pagination.sync="pagination"
                          :rows-per-page-items="rowsPerPageItems"
                          class="elevation-1">
                <template v-slot:items="props">
                    <td class="justify-center layout px-0">
                        <v-icon small class="mr-2" v-on:click="editItem(props.item)">edit</v-icon>
                        <v-icon small v-on:click="deleteItem(props.item)">delete</v-icon>
                    </td>
                    <td>{{ props.item.source_text }}</td>
                    <td>{{ props.item.source_language }}</td>
                    <td>{{ props.item.target_text }}</td>
                    <td>{{ props.item.target_language }}</td>
                </template>
            </v-data-table>

            <!-- Start Dialog editing item -->
            <v-dialog v-model="dialog" max-width="1600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">
                            <v-icon>edit</v-icon>
                            {{ formTitle }}
                        </span>
                    </v-card-title>

                    <v-card-text>
                        <v-container>
                            <v-layout wrap>
                                <v-flex xs12>
                                    <v-text-field v-model="editedItem.source_language"
                                                  label="Source language"
                                                  prepend-icon="language"
                                                  readonly></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-textarea v-model="editedItem.source_text"
                                                label="Source text"
                                                prepend-icon="text_fields"
                                                outline readonly></v-textarea>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field v-model="editedItem.target_language"
                                                  label="Target language"
                                                  prepend-icon="language"
                                                  readonly></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-textarea v-model="editedItem.target_text"
                                                label="Target text"
                                                prepend-icon="text_fields"
                                                outline></v-textarea>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" flat v-on:click="close">Cancel</v-btn>
                        <v-btn color="blue darken-1" flat v-on:click="save">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!-- End Dialog editing item -->

        </v-flex>
    </v-layout>
</template>

<script>

export default {
    name: 'translation-list',
    data: () => ({
        translations: [],
        headers: [
            { text: '', sortable: false },
            { text: 'Source text', value: 'source_text', sortable: false },
            { text: 'Source language', value: 'source_language', sortable: true },
            { text: 'Target text', value: 'target_text', sortable: false },
            { text: 'Target language', value: 'target_language', sortable:true }
        ],
        pagination: {
            rowsPerPage: 25,
        },
        rowsPerPageItems: [ 10, 25, 50 ],
        dialog: false,
        defaultItem: {
            source_text: '',
            source_language: '',
            target_text: '',
            target_language: '',
        },
        editedIndex: -1,
        editedItem: {
            source_text: '',
            source_language: '',
            target_text: '',
            target_language: '',
        },
    }),
    mounted () {
        let url = '/translation'
        this.$api.get(url).then((response) => {
            if(response.status === 200) {
                this.translations = response.data.data
            }
        }, (error) => {
            console.log(error)
        })
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
        }
    },
    watch: {
        dialog (val) {
            val || this.close()
        }
    },
    methods: {
        close () {
            this.dialog = false
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            }, 2000)
        },

        editItem (item) {
            this.editedIndex = this.translations.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            let index = this.translations.indexOf(item)

            if(confirm('Are you sure you want to delete this item?')) {
                this.$api.delete('/translation', {
                    data: {
                        source_text: item.source_text,
                        source_lang: item.source_language,
                        target_lang: item.target_language
                    }
                }).then((response) => {
                    if(response.status === 200) {
                        this.translations.splice(index, 1)
                    }
                }, (error) => {
                    console.log(error)
                })
            }
        },

        save () {
            if (this.editedIndex > -1) {
                this.$api.patch('/translation', {
                    source_text: this.editedItem.source_text,
                    source_lang: this.editedItem.source_language,
                    target_text: this.editedItem.target_text,
                    target_lang: this.editedItem.target_language
                }).then((response) => {
                    if (response.status === 200) {
                        Object.assign(this.translations[this.editedIndex], this.editedItem)
                    }
                }, (error) => {
                    console.log(error)
                })

            } else {
                // Use this to create a new item
                //this.translations.push(this.editedItem)
            }

            this.close()
        }
    }
}

</script>
