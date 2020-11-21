<template>
    <div>
        <!-- Delete Alert Modal -->
        <Modal 
        :value="getDeleteModalObj.showDeleteModal" width="360"
        :mask-closable="false"
		:closable="false">
            <p slot="header" style="color:#f60;text-align:center">
                <Icon type="ios-information-circle"></Icon>
                <span>Delete confirmation</span>
            </p>
            <div style="text-align:center">
                <p>{{getDeleteModalObj.msg}}</p>
            </div>
            <div slot="footer">
                <Button type="default" size="large" @click="closeModal">Close</Button>
                <Button type="error" size="large" :loading="isDeleting" :disabled="isDeleting" @click="deleteTag">Delete</Button>
            </div>
        </Modal>
        <!-- End Delete Alert Modal -->
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
export default {
    data(){
        return{
            isDeleting : false,
        }
    },
    methods : {
        async deleteTag(){
			this.isDeleting = true
			const res = await this.callApi('post', this.getDeleteModalObj.deleteUrl, this.getDeleteModalObj.data)
			if (res.status === 200) {
                this.s(this.getDeleteModalObj.successMsg)
                this.$store.commit('setDeleteModal', true)
			}else{
                this.swr()
                this.$store.commit('setDeleteModal', false)
			}
			this.isDeleting = false
        },
        closeModal(){
            this.$store.commit('setDeleteModal', false)
        },
    },
    computed : {
        ...mapGetters(['getDeleteModalObj'])
    },
}
</script>