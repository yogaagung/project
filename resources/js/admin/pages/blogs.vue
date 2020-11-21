<template>
    <div>
        <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Blogs <Button @click="$router.push('/createArticle')"><Icon type="md-add" /> Create Blog</Button></p>
					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Title</th>
								<th>Category</th>
								<th>Tag</th>
								<th>View</th>
                                <th>Created at</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->

								<!-- ITEMS -->
							<tr v-for="(blog, i) in blogs" :key="i" v-if="blogs.length">
								<td>{{i+1}}</td>
								<td class="_table_name">{{blog.title}}</td>
                                <td><span v-for="(c, j) in blog.cat" :key="j" v-if="blog.cat.length"><Tag color="geekblue">{{c.categoryName}}</Tag></span></td>
                                <td><span v-for="(t, j) in blog.tag" :key="j" v-if="blog.tag.length"><Tag color="geekblue">{{t.tagName}}</Tag></span></td>
                                <td>{{blog.views}}</td>
								<td>{{blog.created_at | timeformat}}</td>
								<td>
									<Button type="info" size="small" long>Visit Blog</Button><br>
									<Button type="warning" size="small" long @click="$router.push(`/editblog/${blog.id}`)" v-if="isUpdatePermitted">Edit</Button>
									<Button type="error" size="small" long @click="showDeleteingModal(blog, i)" :loading="blog.isDeleting" v-if="isDeletePermitted">Delete</Button>
                                </td>
							</tr>
							<Page :total="pageInfo.total" :current="pageInfo.current_page" :page-size="parseInt(pageInfo.per_page)" @on-change="getBlogData" v-if="pageInfo"/>
								<!-- ITEMS -->
						</table>
					</div>
				</div>
				<!-- Delete Alert Modal -->
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
			isAdding : false, 
			tags : [],
			index : -1,
			showDeleteModal : false,
			isDeleting : false,
			deleteItem : {},
            deletingIndex : -1,
			blogs : [],
			total : 1,
			pageInfo : null,
		}
	},
	methods:{
		showDeleteingModal(blog, i){
            this.deletingIndex = i
            const deleteModalObj = {
				showDeleteModal : true,
				deleteUrl : 'app/delete_blog',
				data : {id : blog.id},
				deletingIndex : i,
                isDeleted : false,
                msg : 'Are you sure to delete this blog?',
                successMsg : 'Blog Has Been Delete Successfully!'
			}
			this.$store.commit('setDeletingModalObj', deleteModalObj)
			// this.deleteItem = tag
			// this.deletingIndex = i
			// this.showDeleteModal = true
		},
		async getBlogData(page = 1){
			const res = await this.callApi('get', `app/blogsdata?page=${page}&total=${this.total}`)
			if (res.status == 200) {
				this.blogs = res.data.data
				this.pageInfo = res.data
			}else{
				this.swr()
			}
		}

	},

	async created(){
		this.getBlogData()
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
				this.blogs.splice(this.deletingIndex, 1)
			}
		}
	},

}
</script>