Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="导航标题" prop="renovation_name">
							<el-input  v-model="form.renovation_name" autoComplete="off" clearable maxlength="4" show-word-limit placeholder="请输入导航标题"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="导航图片" prop="renovation_image">
							<Upload v-if="show" size="small"      file_type="image"  :image.sync="form.renovation_image"></Upload>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="导航页面" prop="renovation_page">
							<el-select @change="selectRenovation_place"  style="width:100%" v-model="form.renovation_page" filterable clearable placeholder="请选择导航页面">
								<el-option v-for="(item,i) in renovation_pages" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="导航位置" prop="renovation_place">
							<el-select   style="width:100%" v-model="form.renovation_place" filterable clearable placeholder="请选择导航位置">
								<el-option v-for="(item,i) in renovation_places" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="导航分类" prop="renovation_type">
							<el-select   style="width:100%" v-model="form.renovation_type" filterable clearable placeholder="请选择导航分类">
								<el-option v-for="(item,i) in renovation_types" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="导航简介" prop="renovation_synopsis">
							<el-input  v-model="form.renovation_synopsis" autoComplete="off" clearable  placeholder="请输入导航简介"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="附加参数" prop="renovation_extra">
							<key-data v-if="show" :item.sync="form.renovation_extra"></key-data>
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
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				renovation_name:'',
				renovation_image:'',
				renovation_page:'',
				renovation_place:'',
				renovation_type:'',
				renovation_synopsis:'',
				renovation_extra:[{}],
				shop_id:'',
			},
			renovation_pages:[],
			renovation_places:[],
			renovation_types:[],
			loading:false,
			rules: {
				renovation_name:[
					{required: true, message: '导航标题不能为空', trigger: 'blur'},
				],
				renovation_image:[
					{required: true, message: '导航图片不能为空', trigger: 'blur'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Renovation/getRenovation_place',{renovation_page:this.info.renovation_page}).then(res => {
					if(res.data.status == 200){
						this.renovation_places = res.data.data
					}
				})
			}
			if(val){
				axios.post(base_url + '/Renovation/getFieldList').then(res => {
					if(res.data.status == 200){
						this.renovation_pages = res.data.data.renovation_pages
						this.renovation_types = res.data.data.renovation_types
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.setDefaultVal('renovation_extra')
		},
		selectRenovation_place(val){
			this.form.renovation_place = ''
			axios.post(base_url + '/Renovation/getRenovation_place',{renovation_page:val}).then(res => {
				if(res.data.status == 200){
					this.renovation_places = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Renovation/update',this.form).then(res => {
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
		setDefaultVal(key){
			if(this.form[key] == null || this.form[key] == ''){
				this.form[key] = []
			}
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
			this.form.renovation_extra = [{}]
		},
	}
})
