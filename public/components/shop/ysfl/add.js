Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="名称/分类" prop="ysfl_name">
							<el-input  v-model="form.ysfl_name" autoComplete="off" clearable  placeholder="请输入名称/分类"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属分类" prop="ysfl_pid">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.ysfl_pid" :options="ysfl_pids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择所属分类"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="钥匙详单" prop="ysfl_xiangdan">
							<key-data v-if="show" :item.sync="form.ysfl_xiangdan"></key-data>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="钥匙备注" prop="ysfl_beizhu">
							<el-input  type="textarea" autoComplete="off" v-model="form.ysfl_beizhu"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入钥匙备注"/>
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
		'treeselect':VueTreeselect.Treeselect,
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
				shop_id:'',
				xqgl_id:'',
				ysfl_name:'',
				ysfl_xiangdan:[{}],
				ysfl_beizhu:'',
			},
			ysfl_pids:[],
			loading:false,
			rules: {
				ysfl_name:[
					{required: true, message: '名称/分类不能为空', trigger: 'blur'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Ysfl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.ysfl_pids = res.data.data.ysfl_pids
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
					axios.post(base_url + '/Ysfl/add',this.form).then(res => {
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
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
			this.form.ysfl_xiangdan = [{}]
		},
	}
})
