Vue.component('Add222', {
	template: `
		<el-drawer title="添加"  direction="rtl" size="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="下多" prop="fcxx_id">
							<el-select multiple style="width:100%" v-model="form.fcxx_id" filterable clearable placeholder="请选择下多">
								<el-option v-for="(item,i) in fcxx_ids" :key="i" :label="item.key" :value="item.val.toString()"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开资项目" prop="xqgl_id">
							<el-select @change="selectZzgl_id"  style="width:100%" v-model="form.xqgl_id" filterable clearable placeholder="请选择开资项目">
								<el-option v-for="(item,i) in xqgl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属部门" prop="zzgl_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.zzgl_id" :options="zzgl_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择所属部门"/>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" style="text-align:center;margin:0 0 30px 0">
				<el-button :size="size" style="width:35%;" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" style="width:35%;" @click="closeForm">取 消</el-button>
			</div>
			</div>
		</el-drawer>
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
				fcxx_id:[],
				xqgl_id:'',
			},
			fcxx_ids:[],
			xqgl_ids:[],
			zzgl_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Xldx/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fcxx_ids = res.data.data.fcxx_ids
						this.xqgl_ids = res.data.data.xqgl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
		},
		selectZzgl_id(val){
			this.$delete(this.form,'zzgl_id')
			axios.post(base_url + '/Xldx/getZzgl_id',{xqgl_id:val}).then(res => {
				if(res.data.status == 200){
					this.zzgl_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Xldx/add222',this.form).then(res => {
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
		},
	}
})
