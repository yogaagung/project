<template>
    <div>
        <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Role Management <Button @click="addModal=true" v-if="isWritePermitted"><Icon type="md-add" /> Add a New Role</Button></p>
					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Role Type</th>
								<th>Created at</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->

								<!-- ITEMS -->
							<tr v-for="(role, i) in roles" :key="i" v-if="roles.length">
								<td>{{i+1}}</td>
								<td class="_table_name">{{role.roleName}}</td>
								<td>{{role.created_at | timeformat}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(role, i)"  v-if="isUpdatePermitted">Edit</Button>
									<Button type="error" size="small" @click="showDeleteingModal(role, i)" :loading="role.isDeleting" v-if="isDeletePermitted">Delete</Button>
								</td>
							</tr>
								<!-- ITEMS -->
						</table>
					</div>
				</div>
				<!-- Add Role Modal -->
				<Modal
					v-model="addModal"
					title="Add Role"
					:mask-closable="false"
					:closable="false"
					>
					<Input v-model="data.roleName" placeholder="Role Name"/>
					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="add" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding...' : 'Add Role'}}</Button>
					</div>
				</Modal>
				<!-- End Add Role Modal -->

				<!-- Role Edit Modal -->
				<Modal
					v-model="editModal"
					title="Edit Role"
					:mask-closable="false"
					:closable="false"
					>
					<Input v-model="editData.roleName" placeholder="Edit Role Name"/>
					<div slot="footer">
						<Button type="default" @click="editModal=false">Close</Button>
						<Button type="primary" @click="edit" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing...' : 'Edit Role'}}</Button>
					</div>
				</Modal>
				<!-- End Role Edit Modal -->

				<!-- Delete Alert Modal -->
				<!-- <Modal v-model="showDeleteModal" width="360">
					<p slot="header" style="color:#f60;text-align:center">
						<Icon type="ios-information-circle"></Icon>
						<span>Delete confirmation</span>
					</p>
					<div style="text-align:center">
						<p>Are you sure want to delete?</p>
					</div>
					<div slot="footer">
						<Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteTag">Delete</Button>
					</div>
				</Modal> -->
				<deleteModal></deleteModal>
				<!-- End Delete Alert Modal -->
			</div>
		</div>
    </div>
</template>

<script>
import deleteModal from '../components/deleteModal.vue'
import {mapGetters} from 'vuex'
export default {
	data(){
		return{
			data :{
				roleName: ''
			},
			addModal : false,
			editModal : false,
			isAdding : false, 
			roles : [],
			editData : {
				roleName: ""
			},
			index : -1,
			showDeleteModal : false,
			isDeleting : false,
			deleteItem : {},
			deletingIndex : -1,
			websiteSettings: [], 
		}
	},
	methods:{
		async add(){
			if (this.data.roleName.trim()=='') return this.e('Role Name is Required')
			const res = await this.callApi('post', 'app/create_role', this.data)
			if (res.status === 201) {
				this.roles.unshift(res.data)
				this.s('Role has been Added Successfully')
				this.addModal = false
				this.data.roleName = ''
			}else{
				if (res.status == 422){
					if (res.data.errors.roleName) {
						this.e(res.data.errors.roleName[0])
					}
				}else{
					this.swr()
				}
			}
		},
		async edit(){
			if (this.editData.roleName.trim()=='') return this.e('Role Name is Required')
			const res = await this.callApi('post', 'app/edit_role', this.editData)
			if (res.status === 200) {
				this.roles[this.index].roleName = this.editData.roleName
				this.s('Role Name has been Edited Successfully')
				this.editModal = false
			}else{
				if (res.status == 422){
					if (res.data.errors.roleName) {
						this.e(res.data.errors.roleName[0])
					}
				}else{
					this.swr()
				}
			}
		},
		showEditModal(role, index){
			let obj = {
				id : role.id,
				roleName : role.roleName,
			}
			this.editData = obj
			this.editModal = true
			this.index = index
		},
		async deleteTag(){
			this.isDeleting = true
			const res = await this.callApi('post', 'app/delete_tag', this.deleteItem)
			if (res.status === 200) {
				this.tags.splice(this.deletingIndex, 1)
				this.s('Tag has been Deleted Successfully')
			}else{
				this.swr()
			}
			this.isDeleting = false
			this.showDeleteModal = false
		},
		showDeleteingModal(tag, i){
            this.deletingIndex = i
			const deleteModalObj = {
				showDeleteModal : true,
				deleteUrl : 'app/delete_tag',
				data : tag,
				deletingIndex : i,
				isDeleted : false,
				msg : 'Are you sure to delete this role?',
                successMsg : 'Role Has Been Delete Successfully!'
			}
			this.$store.commit('setDeletingModalObj', deleteModalObj)
			// this.deleteItem = tag
			// this.deletingIndex = i
			// this.showDeleteModal = true
		}

	},

	async created(){
		const res = await this.callApi('get', 'app/get_roles')
		if (res.status == 200) {
			this.roles = res.data
		}else{
			this.swr()
		}
	},
	components:{
		deleteModal
	},
	computed : {
		...mapGetters(['getDeleteModalObj'])
	},
	watch : {
		getDeleteModalObj(obj){
			if (obj.isDeleted) {
				this.tags.splice(this.deletingIndex, 1)
			}
		}
	},

}
</script>