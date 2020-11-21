<template>
    <div>
        <h1>Hello {{views}}</h1>
        <button @click="updateCounter(1)">Increase</button>
        <button @click="updateCounter(-1)">Decrease</button>
        <br>
        <br><br>

        <div v-for="(blog, i) in blogs" :key="i" v-if="blogs.length">
            <h1>{{blog.title}}</h1>
            <p>{{blog.post}}</p>
        </div>
        <br>
        <br>
        <br>
        <div>
            <h1 v-if="showItem">Show Item If "showItem" is true</h1>
            <p v-else>Show Item If "showItem" is false</p>
        </div>
        <button @click="showItem = !showItem">Change ShowItem</button>
    </div>
</template>

<script>
export default {
    data(){
        return {
            views : 0,
            blogs : [],
            showItem : false,
        }
    },
    methods:{
        updateCounter(number){
            this.views += number
        },
        // ChangeItem(){
        //     this.showItem = !this.showItem
        // }
    },
    created(){
        this.views = 100

        let posts = [
                {title: 'Blog', post: 'This Post 1', id: 1},
                {title: 'Blog', post: 'This Post 2', id: 2},
                {title: 'Blog', post: 'This Post 3', id: 3},
                {title: 'Blog', post: 'This Post 4', id: 4},
                {title: 'Blog', post: 'This Post 5', id: 5},]

        this.blogs = posts
    }
}
</script>