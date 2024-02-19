Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="文章标题" prop="wzgl_title">
							<el-input  v-model="form.wzgl_title" autoComplete="off" clearable  placeholder="请输入文章标题"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="文章首图" prop="wzgl_img">
							<Upload v-if="show" size="small"      file_type="image" :image.sync="form.wzgl_img"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="文章简述" prop="wzgl_futitle">
							<el-input  type="textarea" autoComplete="off" v-model="form.wzgl_futitle"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入文章简述"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="文章内容" prop="wzgl_neirong" v-if="show">
							<tinymce  :content.sync="form.wzgl_neirong"></tinymce>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="发布时间" prop="wzgl_time">
							<el-date-picker value-format="yyyy-MM-dd HH:mm:ss" type="datetime" v-model="form.wzgl_time" clearable placeholder="请输入发布时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属分类" prop="wzfl_id">
							<el-select   style="width:100%" v-model="form.wzfl_id" filterable clearable placeholder="请选择所属分类">
								<el-option v-for="(item,i) in wzfl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
	},
	data(){
		return {
			form: {
				wzgl_title:'',
				wzgl_img:'',
				wzgl_futitle:'',
				wzgl_neirong:'',
				wzgl_time:curentTime(),
				shop_id:'',
				xqgl_id:'',
				wzfl_id:'',
			},
			wzfl_ids:[],
			loading:false,
			rules: {
				wzgl_title:[
					{required: true, message: '文章标题不能为空', trigger: 'blur'},
				],
				wzgl_img:[
					{required: true, message: '文章首图不能为空', trigger: 'blur'},
				],
				wzgl_futitle:[
					{required: true, message: '文章简述不能为空', trigger: 'blur'},
				],
				wzgl_neirong:[
					{required: true, message: '文章内容不能为空', trigger: 'blur'},
				],
				wzfl_id:[
					{required: true, message: '所属分类不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Wzgl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.wzfl_ids = res.data.data.wzfl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Wzgl/add',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
