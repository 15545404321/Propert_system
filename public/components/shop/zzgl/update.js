Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="部门名称" prop="zzgl_bmmc">
							<el-input  v-model="form.zzgl_bmmc" autoComplete="off" clearable  placeholder="请输入部门名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属项目" prop="xqgl_id">
							<el-select @change="selectZzgl_sjbm"  style="width:100%" v-model="form.xqgl_id" filterable clearable placeholder="请选择所属项目">
								<el-option v-for="(item,i) in xqgl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="上级部门" prop="zzgl_sjbm">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.zzgl_sjbm" :options="zzgl_sjbms" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择上级部门"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="部门排序" prop="zzgl_px">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.zzgl_px" clearable :min="0" placeholder="请输入部门排序"/>
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
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				shop_id:'',
				zzgl_bmmc:'',
				xqgl_id:'',
				zzgl_px:50,
			},
			xqgl_ids:[],
			zzgl_sjbms:[],
			loading:false,
			rules: {
				zzgl_bmmc:[
					{required: true, message: '部门名称不能为空', trigger: 'blur'},
				],
				xqgl_id:[
					{required: true, message: '所属项目不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Zzgl/getZzgl_sjbm',{xqgl_id:this.info.xqgl_id}).then(res => {
					if(res.data.status == 200){
						this.zzgl_sjbms = res.data.data
					}
				})
			}
			if(val){
				axios.post(base_url + '/Zzgl/getFieldList').then(res => {
					if(res.data.status == 200){
						this.xqgl_ids = res.data.data.xqgl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
		},
		selectZzgl_sjbm(val){
			this.$delete(this.form,'zzgl_sjbm')
			axios.post(base_url + '/Zzgl/getZzgl_sjbm',{xqgl_id:val}).then(res => {
				if(res.data.status == 200){
					this.zzgl_sjbms = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Zzgl/update',this.form).then(res => {
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
